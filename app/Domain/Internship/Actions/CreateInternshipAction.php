<?php

namespace App\Domain\Internship\Actions;

use App\Domain\Internship\DTOs\CreateInternshipDTO;
use App\Domain\Internship\Services\InternshipResolverService;
use App\Enums\InternshipStatus;
use App\Models\Applicant;
use App\Models\ApplicantInternship;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateInternshipAction
{
    public function __construct(
        private readonly InternshipResolverService $resolver,
        private readonly SyncGuarantorsAction $syncGuarantorsAction,
    ) {}

    public function execute(CreateInternshipDTO $dto): ApplicantInternship
    {
        return DB::transaction(function () use ($dto) {
            $applicant = Applicant::findOrFail($dto->applicantId);
            $program = $this->resolver->resolveProgram($dto->internshipProgramId, $dto->programType);

            if (! $program && ! $dto->receivingCompanyId) {
                throw ValidationException::withMessages([
                    'internship_program_id' => 'An internship program or receiving company is required.',
                ]);
            }

            ApplicantInternship::query()
                ->where('applicant_id', $applicant->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            if (! empty($dto->guarantors)) {
                $this->syncGuarantorsAction->execute(
                    new \App\Domain\Internship\DTOs\SyncGuarantorsDTO(
                        applicantId: $applicant->id,
                        guarantors: $dto->guarantors,
                    )
                );
            }

            return ApplicantInternship::create([
                'applicant_id'           => $applicant->id,
                'batch_id'               => $dto->batchId,
                'internship_program_id'  => $program?->id,
                'program_type'           => $dto->programType,
                'status'                 => InternshipStatus::Active,
                'is_current'             => true,
                'dispatching_company_id' => $dto->dispatchingCompanyId ?? $program?->dispatching_company_id,
                'accepting_company_id'   => $dto->acceptingCompanyId ?? $program?->accepting_company_id,
                'receiving_company_id'   => $dto->receivingCompanyId ?? $program?->default_receiving_company_id,
                'job_description'        => $dto->jobDescription ?? $program?->default_job_description,
                'place_of_internship'    => $dto->placeOfInternship,
                'municipality'           => $dto->municipality ?? $program?->default_municipality,
                'agreement_date'         => $dto->agreementDate ?? now()->toDateString(),
                'contract_start'         => $dto->contractStart,
                'contract_end'           => $dto->contractEnd,
                'contract_years'         => $dto->contractYears ?? $program?->contract_years,
                'stipend_amount'         => $dto->stipendAmount ?? $program?->stipend_amount,
                'meal_allowance_amount'  => $dto->mealAllowanceAmount ?? $program?->meal_allowance_amount,
                'work_days'              => $dto->workDays ?? $program?->work_days,
                'day_off'                => $dto->dayOff ?? $program?->day_off,
                'time_start'             => $dto->timeStart ?? $program?->time_start,
                'time_end'               => $dto->timeEnd ?? $program?->time_end,
                'lunch_break'            => $dto->lunchBreak ?? $program?->lunch_break,
                'created_by'             => $dto->createdBy,
            ]);
        });
    }
}