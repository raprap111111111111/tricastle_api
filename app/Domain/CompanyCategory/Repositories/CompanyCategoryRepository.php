<?php

// app/Domain/CompanyCategory/Repositories/CompanyCategoryRepository.php

namespace App\Domain\CompanyCategory\Repositories;

use App\Models\CompanyCategory;
use App\Support\Query\BaseRepository;

class CompanyCategoryRepository extends BaseRepository
{
    protected string $model = CompanyCategory::class;

    protected array $relations = [];

    protected array $searchable = [
        'name',
        'slug',
        'description',
    ];

    protected array $filterable = [
        'is_active',
        'slug',
    ];

    protected array $sortable = [
        'id',
        'name',
        'slug',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function create(array $data): CompanyCategory
    {
        return CompanyCategory::create($data);
    }

    public function update(int $id, array $data): CompanyCategory
    {
        $category = CompanyCategory::findOrFail($id);
        $category->update($data);

        return $category->refresh();
    }

    public function delete(int $id): bool
    {
        return (bool) CompanyCategory::findOrFail($id)->delete();
    }
}