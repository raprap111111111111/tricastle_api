<?php

namespace App\Domain\Internship\Actions;

use App\Domain\Internship\DTOs\ChangeCompanyDTO;
use App\Enums\InternshipProgramType;
use App\Enums\InternshipStatus;
use App\Models\ApplicantInternship;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangeInternshipCompanyAction
{
    public function execute(ApplicantInternship $current, ChangeCompanyDTO $dto): ApplicantInternship
    {
        if (! $current->canChangeCompany()) {
            throw ValidationException::withMessages([
                'internship' => 'This internship cannot change company (not SSW/transferable or not current/active).',
            ]);
        }

        if ((int) $current->receiving_company_id === (int) $dto->receivingCompanyId) {
            throw ValidationException::withMessages([
                'receiving_company_id' => 'New receiving company must be different.',
            ]);
        }

        return DB::transaction(function () use ($current, $dto) {
            $current->update([
                'is_current'    => false,
                'status'        => InternshipStatus::Transferred,
                'change_reason' => $dto->changeReason,
                'changed_at'    => now(),
            ]);

            return ApplicantInternship::create([
                'applicant_id'           => $current->applicant_id,
                'batch_id'               => $dto->batchId ?? $current->batch_id,
                'internship_program_id'  => $dto->internshipProgramId ?? $current->internship_program_id,
                'program_type'           => InternshipProgramType::SswTransfer,
                'status'                 => InternshipStatus::Active,
                'is_current'             => true,
                'dispatching_company_id' => $current->dispatching_company_id,
                'accepting_company_id'   => $dto->acceptingCompanyId ?? $current->accepting_company_id,
                'receiving_company_id'   => $dto->receivingCompanyId,
                'previous_internship_id' => $current->id,
                'job_description'        => $dto->jobDescription ?? $current->job_description,
                'place_of_internship'    => $dto->placeOfInternship,
                'municipality'           => $current->municipality,
                'agreement_date'         => now()->toDateString(),
                'contract_start'         => $dto->contractStart,
                'contract_end'           => $dto->contractEnd,
                'contract_years'         => $dto->contractYears ?? $current->contract_years,
                'stipend_amount'         => $dto->stipendAmount ?? $current->stipend_amount,
                'meal_allowance_amount'  => $dto->mealAllowanceAmount ?? $current->meal_allowance_amount,
                'work_days'              => $current->work_days,
                'day_off'                => $current->day_off,
                'time_start'             => $current->time_start,
                'time_end'               => $current->time_end,
                'lunch_break'            => $current->lunch_break,
                'change_reason'          => $dto->changeReason,
                'changed_at'             => now(),
                'created_by'             => $dto->changedBy,
            ]);
        });
    }
}