<?php

namespace App\Domain\DocumentType\Repositories;

use App\Models\DocumentType;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DocumentTypeRepository extends BaseRepository
{
    protected string $model = DocumentType::class;

    protected array $relations  = [];
    protected array $searchable = [
        'name',
        'code',
        'description',
    ];
    protected array $filterable = [
        'is_active',
        'is_required',
        'category',
    ];
    protected array $sortable = [
        'id',
        'name',
        'code',
        'category',
        'sort_order',
        'validity_days',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'sort_order';
    protected string $defaultOrderDirection = 'asc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->boolean('active_only')) {
            $query->active();
        }

        if ($request->boolean('required_only')) {
            $query->required();
        }

        return $query;
    }
}