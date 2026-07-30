<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\DTOs\UpdateBatchStatusDTO;
use App\Domain\Batch\Repositories\BatchRepository;
use App\Models\Batch;

class UpdateBatchStatusAction
{
    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(Batch $batch, UpdateBatchStatusDTO $dto): Batch
    {
        return $this->repository->update($batch->id, [
            'status' => $dto->status,
        ]);
    }
}