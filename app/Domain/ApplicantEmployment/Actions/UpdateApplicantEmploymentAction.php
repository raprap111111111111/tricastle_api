<?php

namespace App\Domain\ApplicantEmployment\Actions;

use App\Domain\ApplicantEmployment\DTOs\UpdateApplicantEmploymentDTO;
use App\Domain\ApplicantEmployment\Repositories\ApplicantEmploymentRepository;
use App\Models\ApplicantEmployment;
use Illuminate\Support\Facades\DB;

class UpdateApplicantEmploymentAction
{
    public function __construct(
        private readonly ApplicantEmploymentRepository $repository
    ) {}

    public function execute(ApplicantEmployment $employment, UpdateApplicantEmploymentDTO $dto): ApplicantEmployment
    {
        return DB::transaction(function () use ($employment, $dto) {
            // If becoming current, un-mark all other current records for this applicant
            if ($dto->isCurrent === true) {
                ApplicantEmployment::where('applicant_id', $employment->applicant_id)
                    ->where('id', '!=', $employment->id)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }

            $payload = array_filter([
                'company_name'       => $dto->companyName,
                'position'           => $dto->position,
                'industry'           => $dto->industry,
                'job_description'    => $dto->jobDescription,
                'date_started'       => $dto->dateStarted,
                'date_ended'         => $dto->dateEnded,
                'is_current'         => $dto->isCurrent,
                'country'            => $dto->country,
                'city'               => $dto->city,
                'salary'             => $dto->salary,
                'salary_currency'    => $dto->salaryCurrency,
                'reason_for_leaving' => $dto->reasonForLeaving,
            ], fn ($value) => $value !== null);

            // Clear date_ended if switching to current
            if ($dto->isCurrent === true) {
                $payload['date_ended'] = null;
            }

            return $this->repository->update($employment->id, $payload);
        });
    }
}