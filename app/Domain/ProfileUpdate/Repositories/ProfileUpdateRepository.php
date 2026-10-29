<?php

namespace App\Domain\ProfileUpdate\Repositories;

use App\Models\ApplicantProfileUpdate;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ProfileUpdateRepository extends BaseRepository
{
    protected string $model = ApplicantProfileUpdate::class;

    protected array $relations = [
        'applicant:id,first_name,middle_name,last_name,applicant_code,city,province,mobile,passport_number,status',
        'reviewer:id,first_name,middle_name,last_name',
    ];

    protected array $searchable = [];

    protected array $filterable = [
        'status',
        'applicant_id',
        'reviewed_by',
    ];

    protected array $sortable = [
        'id',
        'status',
        'applicant_id',
        'reviewed_by',
        'reviewed_at',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    /**
     * Override search so staff can find updates by applicant name / code / mobile.
     */
    protected function applySearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->whereHas('applicant', function (Builder $aq) use ($search) {
                $aq->where('first_name', 'LIKE', "%{$search}%")
                   ->orWhere('middle_name', 'LIKE', "%{$search}%")
                   ->orWhere('last_name', 'LIKE', "%{$search}%")
                   ->orWhere('applicant_code', 'LIKE', "%{$search}%")
                   ->orWhere('mobile', 'LIKE', "%{$search}%")
                   ->orWhere('passport_number', 'LIKE', "%{$search}%")
                   ->orWhereRaw(
                       "CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) LIKE ?",
                       ["%{$search}%"]
                   );
            });
        });
    }

    /**
     * Apply additional complex filters like geography checking the related applicant.
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (!empty($filters['province']) || !empty($filters['city']) || !empty($filters['address'])) {
            $query->whereHas('applicant', function (Builder $aq) use ($filters) {
                if (!empty($filters['province'])) {
                    $aq->where('province', $filters['province']);
                }
                if (!empty($filters['city'])) {
                    $aq->where('city', $filters['city']);
                }
                if (!empty($filters['address'])) {
                    $aq->where(function ($q) use ($filters) {
                        $q->where('current_address', 'LIKE', "%{$filters['address']}%")
                          ->orWhere('permanent_address', 'LIKE', "%{$filters['address']}%");
                    });
                }
            });
        }

        return $query;
    }

    /**
     * Convenience wrapper used by controller.
     */
    public function getPaginatedByStatus(array $params = [], ?string $resource = null): array
    {
        if (!isset($params['status']) || $params['status'] === '') {
            $params['status'] = 'pending';
        }

        return $this->paginate($params, $resource);
    }
}