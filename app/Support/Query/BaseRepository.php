<?php

namespace App\Support\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected string $model;

    protected array $relations          = [];
    protected array $searchable         = [];
    protected array $filterable         = [];
    protected array $sortable           = [];

    protected string $defaultOrderBy        = 'id';
    protected string $defaultOrderDirection = 'desc';

    // ==========================================
    // Base Query
    // ==========================================

    public function query(): Builder
    {
        return ($this->model)::query()->with($this->relations);
    }

    // ==========================================
    // Paginate — returns custom format
    // ==========================================

    public function paginate(array $params = [], ?string $resource = null): array
    {
        $query = $this->query();

        $query = $this->applySearch($query, $params['search'] ?? null);
        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting(
            $query,
            $params['order_by']  ?? null,
            $params['order_dir'] ?? null,
        );

        $offset  = (int) ($params['offset']  ?? 0);
        $limit   = (int) ($params['limit']   ?? 10);
        $total   = $query->count();

        $records = $query->skip($offset)->take($limit)->get();

        // Wrap with resource if provided
        if ($resource) {
            $records = $resource::collection($records);
        }

        $currentPage = $limit > 0 ? (int) floor($offset / $limit) + 1 : 1;
        $lastPage    = $limit > 0 ? (int) ceil($total / $limit) : 1;

        return [
            'records'      => $records,
            'total'        => $total,
            'offset'       => $offset,
            'limit'        => $limit,
            'current_page' => $currentPage,
            'last_page'    => $lastPage,
            'per_page'     => $limit,
            'has_more'     => ($offset + $limit) < $total,
        ];
    }

    // ==========================================
    // Search
    // ==========================================

    protected function applySearch(Builder $query, ?string $search): Builder
    {
        if (empty($search) || empty($this->searchable)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            foreach ($this->searchable as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $q->{$method}($column, 'LIKE', "%{$search}%");
            }
        });
    }

    // ==========================================
    // Filters
    // ==========================================

    protected function applyFilters(Builder $query, array $params): Builder
    {
        foreach ($this->filterable as $column) {
            if (isset($params[$column]) && $params[$column] !== '') {
                $query->where($column, $params[$column]);
            }
        }

        return $query;
    }

    // ==========================================
    // Sorting
    // ==========================================

    protected function applySorting(
        Builder $query,
        ?string $orderBy,
        ?string $direction
    ): Builder {
        $column    = in_array($orderBy, $this->sortable)
            ? $orderBy
            : $this->defaultOrderBy;

        $direction = in_array($direction, ['asc', 'desc'])
            ? $direction
            : $this->defaultOrderDirection;

        return $query->orderBy($column, $direction);
    }

    // ==========================================
    // CRUD
    // ==========================================

    public function findById(int $id): ?Model
    {
        return $this->query()->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function create(array $data): Model
    {
        $model = ($this->model)::create($data);

        return $model->load($this->relations);
    }

    public function update(int $id, array $data): Model
    {
        $model = ($this->model)::findOrFail($id);
        $model->update($data);

        return $model->fresh($this->relations);
    }

    public function delete(int $id): bool
    {
        return (bool) ($this->model)::findOrFail($id)->delete();
    }
}