<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\Repositories\BatchRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Batch;

class DeleteBatchAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(Batch $batch): bool
    {
        // Capture info BEFORE deletion
        $name        = $batch->name;
        $batchNumber = $batch->batch_number;
        $wasActive   = $batch->is_active;

        // 🔔 Notify staff BEFORE deletion
        $this->notifyWarning(
            permissions: 'batch.viewAny',
            title:       '🗑️ Batch Deleted',
            message:     "Batch \"{$name}\" (#{$batchNumber}) has been permanently removed.",
            module:      'batch',
        );

        // 🔔 Extra alert if the deleted batch was the ACTIVE one
        if ($wasActive) {
            $this->notifyError(
                permissions: ['batch.viewAny', 'applicant.viewAny'],
                title:       '⚠️ Active Batch Removed',
                message:     "The ACTIVE batch \"{$name}\" was deleted. New applicants will NOT auto-assign until a new batch is activated.",
                module:      'batch',
            );
        }

        return $this->repository->delete($batch->id);
    }
}