<?php

namespace App\Domain\ApplicantEmployment\Actions;

use App\Domain\ApplicantEmployment\DTOs\CreateApplicantEmploymentDTO;
use App\Domain\ApplicantEmployment\Repositories\ApplicantEmploymentRepository;
use App\Models\ApplicantEmployment;
use Illuminate\Support\Facades\DB;

class CreateApplicantEmploymentAction
{
    public function __construct(
        private readonly ApplicantEmploymentRepository $repository
    ) {}

    public function execute(CreateApplicantEmploymentDTO $dto): ApplicantEmployment
    {
        return DB::transaction(function () use ($dto) {
            // If marked as current, un-mark all other current records for this applicant
            if ($dto->isCurrent) {
                ApplicantEmployment::where('applicant_id', $dto->applicantId)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }

            return $this->repository->create([
                'applicant_id'       => $dto->applicantId,
                'company_name'       => $dto->companyName,
                'position'           => $dto->position,
                'industry'           => $dto->industry,
                'job_description'    => $dto->jobDescription,
                'date_started'       => $dto->dateStarted,
                'date_ended'         => $dto->isCurrent ? null : $dto->dateEnded,
                'is_current'         => $dto->isCurrent,
                'country'            => $dto->country,
                'city'               => $dto->city,
                'salary'             => $dto->salary,
                'salary_currency'    => $dto->salaryCurrency,
                'reason_for_leaving' => $dto->reasonForLeaving,
            ]);
        });
    }
}