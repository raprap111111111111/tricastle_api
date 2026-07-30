<?php

// app/Domain/ApplicantTattoo/Repositories/ApplicantTattooRepository.php

namespace App\Domain\ApplicantTattoo\Repositories;

use App\Models\ApplicantTattoo;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ApplicantTattooRepository extends BaseRepository
{
    protected string $model = ApplicantTattoo::class;

    protected array $relations = [
        'applicant',
    ];

    protected array $searchable = [
        'location',
        'description',
    ];

    protected array $filterable = [
        'applicant_id',
        'size',
        'is_visible',
    ];

    protected array $sortable = [
        'id',
        'applicant_id',
        'location',
        'size',
        'is_visible',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    /**
     * Get all tattoos belonging to a specific applicant.
     */
    public function findByApplicantId(int $applicantId): Collection
    {
        return $this->query()
            ->where('applicant_id', $applicantId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}