<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\DTOs\CreateBatchDTO;
use App\Domain\Batch\Repositories\BatchRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class CreateBatchAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(CreateBatchDTO $dto): Batch
    {
        $batch = DB::transaction(function () use ($dto) {

            // ── 1. If this batch is being activated, deactivate all others ──
            if ($dto->isActive) {
                $this->repository->deactivateAll();
            }

            // ── 2. Create the batch ──
            return $this->repository->create([
                'batch_number'    => $dto->batchNumber,
                'name'            => $dto->name,
                'country'         => $dto->country,
                'deployment_date' => $dto->deploymentDate,
                'status'          => $dto->status,
                'is_active'       => $dto->isActive,
                'description'     => $dto->description,
            ]);
        });

        // 🔔 Notify staff who can view batches
        $this->notifySuccess(
            permissions: 'batch.viewAny',
            title:       '🎓 New Batch Created',
            message:     "Batch \"{$batch->name}\" (#{$batch->batch_number}) has been created for {$batch->country}.",
            module:      'batch',
            actionUrl:   "/batches/{$batch->id}",
        );

        // 🔔 If created as ACTIVE, broadcast the activation
        if ($batch->is_active) {
            $this->notifyStaff(
                permissions: ['batch.viewAny', 'applicant.viewAny'],
                title:       '⭐ New Active Batch',
                message:     "Batch \"{$batch->name}\" is now the ACTIVE batch — new applicants will auto-assign here.",
                module:      'batch',
                actionUrl:   "/batches/{$batch->id}",
            );
        }

        return $batch;
    }
}