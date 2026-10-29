<?php

namespace App\Domain\BatchGroup\Repositories;

use App\Models\BatchGroup;
use App\Support\Query\BaseRepository;

class BatchGroupRepository extends BaseRepository
{
    protected string $model = BatchGroup::class;

    protected array $relations = ['applicantBatches'];

    protected array $searchable = ['name'];

    protected array $filterable = ['batch_id', 'color'];

    protected array $sortable = ['id', 'name', 'color', 'max_slots', 'created_at'];

    protected string $defaultOrderBy        = 'id';
    protected string $defaultOrderDirection = 'asc';

    public function getGroupsByBatch(int $batchId)
    {
        return BatchGroup::where('batch_id', $batchId)
            ->withCount('applicantBatches')
            ->orderBy('id', 'asc')
            ->get();
    }
}
