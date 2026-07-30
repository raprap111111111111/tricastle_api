<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Models\Applicant;

class AssignApplicantAction
{
    public function __construct(
        private readonly ApplicantRepository $repository
    ) {}

    public function execute(Applicant $applicant, int $staffId): Applicant
    {
        return $this->repository->update($applicant->id, [
            'assigned_staff_id' => $staffId,
        ]);
    }
}