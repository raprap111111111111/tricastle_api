<?php

namespace App\Domain\ApplicantEducation\Actions;

use App\Domain\ApplicantEducation\DTOs\UpdateApplicantEducationDTO;
use App\Domain\ApplicantEducation\Repositories\ApplicantEducationRepository;
use App\Models\ApplicantEducation;

class UpdateApplicantEducationAction
{
    public function __construct(
        private readonly ApplicantEducationRepository $repository
    ) {}

    public function execute(ApplicantEducation $education, UpdateApplicantEducationDTO $dto): ApplicantEducation
    {
        $payload = array_filter([
            'education_level'  => $dto->educationLevel,
            'education_status' => $dto->educationStatus,
            'school_name'      => $dto->schoolName,
            'course'           => $dto->course,
            'year_started'     => $dto->yearStarted,
            'year_ended'       => $dto->yearEnded,
            'honors'           => $dto->honors,
        ], fn ($value) => $value !== null);

        return $this->repository->update($education->id, $payload);
    }
}