<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\Repositories\BatchRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Batch;
use RuntimeException;

class IncrementBatchSlotsFilledAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(Batch $batch, int $count = 1): Batch
    {
        $newFilled = $batch->slots_filled + $count;

        if ($newFilled > $batch->slots_total) {
            throw new RuntimeException('Cannot exceed total slots for this batch.');
        }

        $updated = $this->repository->update($batch->id, [
            'slots_filled' => $newFilled,
        ]);

        $percentage = $batch->slots_total > 0
            ? round(($newFilled / $batch->slots_total) * 100)
            : 0;

        // 🔔 Alert when batch is FULL
        if ($newFilled >= $batch->slots_total) {
            $this->notifyWarning(
                permissions: ['batch.viewAny', 'applicant.viewAny'],
                title:       '🎯 Batch is FULL',
                message:     "Batch \"{$updated->name}\" (#{$updated->batch_number}) has reached full capacity ({$newFilled}/{$updated->slots_total}). Consider creating a new batch.",
                module:      'batch',
                actionUrl:   "/batches/{$updated->id}",
            );
        }
        // 🔔 Warn when batch is nearly full (90%+)
        elseif ($percentage >= 90) {
            $this->notifyWarning(
                permissions: 'batch.viewAny',
                title:       '⚠️ Batch Almost Full',
                message:     "Batch \"{$updated->name}\" is at {$percentage}% capacity ({$newFilled}/{$updated->slots_total}).",
                module:      'batch',
                actionUrl:   "/batches/{$updated->id}",
            );
        }

        return $updated;
    }
}