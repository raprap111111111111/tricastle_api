<?php

// app/Domain/ApplicantEducation/Repositories/ApplicantEducationRepository.php

namespace App\Domain\ApplicantEducation\Repositories;

use App\Models\ApplicantEducation;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ApplicantEducationRepository extends BaseRepository
{
    protected string $model = ApplicantEducation::class;

    protected array $relations = [
        'applicant',
    ];

    protected array $searchable = [
        'school_name',
        'course',
        'honors',
    ];

    protected array $filterable = [
        'applicant_id',
        'education_level',
        'education_status',
    ];

    protected array $sortable = [
        'id',
        'applicant_id',
        'education_level',
        'education_status',
        'school_name',
        'year_started',
        'year_ended',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'year_ended';
    protected string $defaultOrderDirection = 'desc';

    /**
     * Get all education records for a specific applicant.
     */
    public function findByApplicantId(int $applicantId): Collection
    {
        return $this->query()
            ->where('applicant_id', $applicantId)
            ->orderByRaw('COALESCE(year_ended, year_started) DESC')
            ->get();
    }
}