<?php

namespace App\Domain\Batch\Repositories;

use App\Models\Batch;
use App\Support\Query\BaseRepository;
use Illuminate\Support\Facades\DB;

class BatchRepository extends BaseRepository
{
    protected string $model = Batch::class;

    protected array $relations = [];

    protected array $searchable = [
        'batch_number',
        'name',
        'country',
        'description',
    ];

    protected array $filterable = [
        'country',
        'status',
        'is_active',
    ];

    protected array $sortable = [
        'id',
        'batch_number',
        'name',
        'country',
        'deployment_date',
        'status',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'batch_number';
    protected string $defaultOrderDirection = 'asc';

    // ═══════════════════════════════════════════════════════
    // Active batch methods
    // ═══════════════════════════════════════════════════════

    /**
     * Get the currently active batch (only one exists at a time).
     */
    public function getActiveBatch(): ?Batch
    {
        return Batch::where('is_active', true)->first();
    }

    /**
     * Activate a specific batch (auto-deactivates all others).
     */
    public function activate(int $batchId): ?Batch
    {
        return DB::transaction(function () use ($batchId) {
            // Deactivate all currently active batches
            Batch::where('is_active', true)->update(['is_active' => false]);

            // Activate the target batch
            $batch = Batch::find($batchId);
            if ($batch) {
                $batch->is_active = true;
                $batch->save();
            }

            return $batch?->fresh();
        });
    }

    /**
     * Deactivate a specific batch.
     */
    public function deactivate(int $batchId): ?Batch
    {
        $batch = Batch::find($batchId);
        if ($batch) {
            $batch->is_active = false;
            $batch->save();
        }
        return $batch?->fresh();
    }

    /**
     * Deactivate all batches (no active batch).
     */
    public function deactivateAll(): int
    {
        return Batch::where('is_active', true)
                    ->update(['is_active' => false]);
    }

    /**
     * Check if a batch is currently active.
     */
    public function isActive(int $batchId): bool
    {
        return Batch::where('id', $batchId)
                    ->where('is_active', true)
                    ->exists();
    }
}