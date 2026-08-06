<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\DTOs\UpdateBatchStatusDTO;
use App\Domain\Batch\Repositories\BatchRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Batch;

class UpdateBatchStatusAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(Batch $batch, UpdateBatchStatusDTO $dto): Batch
    {
        $oldStatus = $batch->status;

        $updated = $this->repository->update($batch->id, [
            'status' => $dto->status,
        ]);

        // Format status for display
        $readable = ucwords(str_replace('_', ' ', $dto->status));

        // Pick severity based on the new status
        $severity = match ($dto->status) {
            'deployed', 'completed' => 'success',
            'cancelled', 'failed'   => 'error',
            'on_hold'               => 'warn',
            default                 => 'info',
        };

        // Pick icon based on status
        $icon = match ($dto->status) {
            'deployed'  => '🚀',
            'completed' => '✅',
            'cancelled' => '❌',
            'failed'    => '⚠️',
            'on_hold'   => '⏸️',
            'active'    => '⭐',
            default     => '🔄',
        };

        // 🔔 Notify staff about status change
        $this->notify(
            permissions: ['batch.viewAny', 'applicant.viewAny'],
            title:       "{$icon} Batch Status Changed",
            message:     "Batch \"{$updated->name}\" (#{$updated->batch_number}) is now: {$readable}",
            module:      'batch',
            actionUrl:   "/batches/{$updated->id}",
            severity:    $severity,
            meta:        [
                'batch_id'    => $updated->id,
                'old_status'  => $oldStatus,
                'new_status'  => $dto->status,
            ],
        );

        return $updated;
    }
}