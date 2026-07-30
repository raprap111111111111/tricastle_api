<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\Repositories\BatchRepository;
use App\Models\Batch;
use RuntimeException;

class IncrementBatchSlotsFilledAction
{
    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(Batch $batch, int $count = 1): Batch
    {
        $newFilled = $batch->slots_filled + $count;

        if ($newFilled > $batch->slots_total) {
            throw new RuntimeException('Cannot exceed total slots for this batch.');
        }

        return $this->repository->update($batch->id, [
            'slots_filled' => $newFilled,
        ]);
    }
}