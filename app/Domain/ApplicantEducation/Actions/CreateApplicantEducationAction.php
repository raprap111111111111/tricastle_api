<?php

namespace App\Domain\ApplicantEducation\Actions;

use App\Domain\ApplicantEducation\DTOs\CreateApplicantEducationDTO;
use App\Domain\ApplicantEducation\Repositories\ApplicantEducationRepository;
use App\Models\ApplicantEducation;

class CreateApplicantEducationAction
{
    public function __construct(
        private readonly ApplicantEducationRepository $repository
    ) {}

    public function execute(CreateApplicantEducationDTO $dto): ApplicantEducation
    {
        return $this->repository->create([
            'applicant_id'     => $dto->applicantId,
            'education_level'  => $dto->educationLevel,
            'education_status' => $dto->educationStatus,
            'school_name'      => $dto->schoolName,
            'course'           => $dto->course,
            'year_started'     => $dto->yearStarted,
            'year_ended'       => $dto->yearEnded,
            'honors'           => $dto->honors,
        ]);
    }
}