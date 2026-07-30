<?php

namespace App\Domain\ApplicantEmployment\Actions;

use App\Domain\ApplicantEmployment\Repositories\ApplicantEmploymentRepository;
use App\Models\ApplicantEmployment;

class DeleteApplicantEmploymentAction
{
    public function __construct(
        private readonly ApplicantEmploymentRepository $repository
    ) {}

    public function execute(ApplicantEmployment $employment): bool
    {
        return $this->repository->delete($employment->id);
    }
}