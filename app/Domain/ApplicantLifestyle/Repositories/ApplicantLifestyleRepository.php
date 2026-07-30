<?php

// app/Domain/ApplicantLifestyle/Repositories/ApplicantLifestyleRepository.php

namespace App\Domain\ApplicantLifestyle\Repositories;

use App\Models\ApplicantLifestyle;
use App\Support\Query\BaseRepository;

class ApplicantLifestyleRepository extends BaseRepository
{
    protected string $model = ApplicantLifestyle::class;

    protected array $relations = [
        'applicant',
    ];

    protected array $searchable = [
        'smoking_frequency',
        'drinking_frequency',
        'drugs_notes',
        'medical_notes',
        'allergies_notes',
    ];

    protected array $filterable = [
        'applicant_id',
        'is_smoking',
        'is_drinking_alcohol',
        'is_using_drugs',
        'was_smoking',
        'was_drinking_alcohol',
        'was_using_drugs',
        'has_medical_condition',
        'has_allergies',
    ];

    protected array $sortable = [
        'id',
        'applicant_id',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    /**
     * Upsert (create or update) a lifestyle record for an applicant.
     */
    public function upsertForApplicant(int $applicantId, array $data): ApplicantLifestyle
    {
        $lifestyle = ApplicantLifestyle::updateOrCreate(
            ['applicant_id' => $applicantId],
            $data
        );

        return $lifestyle->fresh($this->relations);
    }

    public function findByApplicantId(int $applicantId): ?ApplicantLifestyle
    {
        return $this->query()->where('applicant_id', $applicantId)->first();
    }
}