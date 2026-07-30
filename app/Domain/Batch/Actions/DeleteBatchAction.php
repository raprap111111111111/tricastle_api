<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\Repositories\BatchRepository;
use App\Models\Batch;

class DeleteBatchAction
{
    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(Batch $batch): bool
    {
        return $this->repository->delete($batch->id);
    }
}