<?php

namespace App\Domain\PassportIssuingOffice\Repositories;

use App\Models\PassportIssuingOffice;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PassportIssuingOfficeRepository extends BaseRepository
{
    protected string $model = PassportIssuingOffice::class;

    protected array $searchable = [
        'code',
        'region',
        'name',
        'address',
        'city',
        'province',
    ];

    protected array $filterable = [
        'region',
        'city',
        'province',
        'is_active',
    ];

    protected array $sortable = [
        'id',
        'code',
        'region',
        'name',
        'city',
        'province',
        'sort_order',
        'created_at',
    ];

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function (Builder $q) use ($search) {
                foreach ($this->searchable as $field) {
                    $q->orWhere($field, 'like', '%' . $search . '%');
                }
            });
        }

        return $query;
    }
}