<?php

namespace App\Domain\ApplicantEmployment\Actions;

use App\Domain\ApplicantEmployment\Repositories\ApplicantEmploymentRepository;
use App\Models\ApplicantEmployment;
use Illuminate\Support\Facades\DB;

class MarkAsCurrentApplicantEmploymentAction
{
    public function __construct(
        private readonly ApplicantEmploymentRepository $repository
    ) {}

    public function execute(ApplicantEmployment $employment): ApplicantEmployment
    {
        return DB::transaction(function () use ($employment) {
            return $this->repository->markAsCurrent(
                $employment->id,
                $employment->applicant_id
            );
        });
    }
}