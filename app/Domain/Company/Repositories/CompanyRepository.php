<?php

// app/Domain/Company/Repositories/CompanyRepository.php

namespace App\Domain\Company\Repositories;

use App\Models\Company;
use App\Support\Query\BaseRepository;

class CompanyRepository extends BaseRepository
{
    protected string $model = Company::class;

    protected array $relations = [
        'category',
    ];

    protected array $searchable = [
        'code',
        'name',
        'name_japanese',
        'city',
        'prefecture',
        'contact_person',
        'contact_email',
        'contact_phone',
    ];

    protected array $filterable = [
        'category_id',
        'prefecture',
        'city',
        'country',
        'is_active',
    ];

    protected array $sortable = [
        'id',
        'code',
        'name',
        'city',
        'prefecture',
        'country',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';
}