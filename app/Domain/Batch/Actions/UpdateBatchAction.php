<?php

namespace App\Domain\Batch\Actions;

use App\Domain\Batch\DTOs\UpdateBatchDTO;
use App\Domain\Batch\Repositories\BatchRepository;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class UpdateBatchAction
{
    public function __construct(
        private readonly BatchRepository $repository
    ) {}

    public function execute(Batch $batch, UpdateBatchDTO $dto): Batch
    {
        return DB::transaction(function () use ($batch, $dto) {

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
    }
}