<?php

// app/Domain/ApplicantEmployment/Repositories/ApplicantEmploymentRepository.php

namespace App\Domain\ApplicantEmployment\Repositories;

use App\Models\ApplicantEmployment;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ApplicantEmploymentRepository extends BaseRepository
{
    protected string $model = ApplicantEmployment::class;

    protected array $relations = [
        'applicant',
    ];

    protected array $searchable = [
        'company_name',
        'position',
        'industry',
        'job_description',
        'city',
        'country',
        'reason_for_leaving',
    ];

    protected array $filterable = [
        'applicant_id',
        'is_current',
        'country',
        'city',
        'salary_currency',
    ];

    protected array $sortable = [
        'id',
        'applicant_id',
        'company_name',
        'position',
        'industry',
        'date_started',
        'date_ended',
        'is_current',
        'salary',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'date_started';
    protected string $defaultOrderDirection = 'desc';

    /**
     * Get all employment records for a specific applicant.
     */
    public function findByApplicantId(int $applicantId): Collection
    {
        return $this->query()
            ->where('applicant_id', $applicantId)
            ->orderBy('is_current', 'desc')
            ->orderBy('date_started', 'desc')
            ->get();
    }

    /**
     * Set a specific employment as the current job for an applicant,
     * and un-mark every other record.
     */
    public function markAsCurrent(int $employmentId, int $applicantId): ApplicantEmployment
    {
        // Un-mark all other records for this applicant
        ApplicantEmployment::where('applicant_id', $applicantId)
            ->where('id', '!=', $employmentId)
            ->update(['is_current' => false]);

        // Mark this one as current & clear its date_ended
        $employment = ApplicantEmployment::findOrFail($employmentId);
        $employment->update([
            'is_current' => true,
            'date_ended' => null,
        ]);

        return $employment->fresh($this->relations);
    }
}