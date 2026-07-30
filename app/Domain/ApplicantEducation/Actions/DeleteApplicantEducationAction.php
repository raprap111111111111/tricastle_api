<?php

namespace App\Domain\ApplicantEducation\Actions;

use App\Domain\ApplicantEducation\Repositories\ApplicantEducationRepository;
use App\Models\ApplicantEducation;

class DeleteApplicantEducationAction
{
    public function __construct(
        private readonly ApplicantEducationRepository $repository
    ) {}

    public function execute(ApplicantEducation $education): bool
    {
        return $this->repository->delete($education->id);
    }
}