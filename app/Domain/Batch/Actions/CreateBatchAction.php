<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\DTOs\CreateBatchDTO;
use App\Domain\Batch\Repositories\BatchRepository;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class CreateBatchAction
{
    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(CreateBatchDTO $dto): Batch
    {
        return DB::transaction(function () use ($dto) {

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
    }
}