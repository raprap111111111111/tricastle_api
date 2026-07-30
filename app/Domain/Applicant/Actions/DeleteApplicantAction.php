<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Models\Applicant;

class DeleteApplicantAction
{
    public function __construct(
        private readonly ApplicantRepository $repository
    ) {}

    public function execute(Applicant $applicant): void
    {
        $this->repository->delete($applicant->id);
    }
}