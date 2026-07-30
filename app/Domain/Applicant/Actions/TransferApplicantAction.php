<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Models\Applicant;

class TransferApplicantAction
{
    public function __construct(
        private readonly ApplicantRepository $repository
    ) {}

    public function execute(Applicant $applicant, int $toStaffId, ?string $reason = null): Applicant
    {
        return $this->repository->update($applicant->id, [
            'assigned_staff_id' => $toStaffId,
        ]);
    }
}