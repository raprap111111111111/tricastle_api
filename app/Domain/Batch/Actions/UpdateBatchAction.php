<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\DTOs\UpdateBatchDTO;
use App\Domain\Batch\Repositories\BatchRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class UpdateBatchAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(Batch $batch, UpdateBatchDTO $dto): Batch
    {
        $wasActive = $batch->is_active;

        $updated = DB::transaction(function () use ($batch, $dto) {

            $data = array_filter([
                'batch_number'    => $dto->batchNumber,
                'name'            => $dto->name,
                'country'         => $dto->country,
                'deployment_date' => $dto->deploymentDate,
                'status'          => $dto->status,
                'description'     => $dto->description,
            ], fn ($v) => $v !== null);

            // ── Handle is_active separately (bool can be false) ──
            if ($dto->isActive !== null) {
                // If activating → deactivate all others first
                if ($dto->isActive === true) {
                    Batch::where('id', '!=', $batch->id)
                         ->where('is_active', true)
                         ->update(['is_active' => false]);
                }
                $data['is_active'] = $dto->isActive;
            }

            $batch->update($data);

            return $batch->fresh();
        });

        // 🔔 Basic update notification
        $this->notifyStaff(
            permissions: 'batch.viewAny',
            title:       '✏️ Batch Updated',
            message:     "Batch \"{$updated->name}\" (#{$updated->batch_number}) has been modified.",
            module:      'batch',
            actionUrl:   "/batches/{$updated->id}",
        );

        // 🔔 Special notification if activation state changed
        if (!$wasActive && $updated->is_active) {
            // Just got activated
            $this->notifySuccess(
                permissions: ['batch.viewAny', 'applicant.viewAny'],
                title:       '⭐ Batch Activated',
                message:     "Batch \"{$updated->name}\" is now the ACTIVE batch — new applicants will auto-assign here.",
                module:      'batch',
                actionUrl:   "/batches/{$updated->id}",
            );
        } elseif ($wasActive && !$updated->is_active) {
            // Just got deactivated
            $this->notifyWarning(
                permissions: 'batch.viewAny',
                title:       '⏸️ Batch Deactivated',
                message:     "Batch \"{$updated->name}\" is no longer the active batch.",
                module:      'batch',
                actionUrl:   "/batches/{$updated->id}",
            );
        }

        return $updated;
    }
}