<?php

namespace App\Domain\Internship\Actions;

use App\Domain\Internship\DTOs\UpdateInternshipDTO;
use App\Models\ApplicantInternship;
use Illuminate\Support\Facades\DB;

class UpdateInternshipAction
{
    public function execute(ApplicantInternship $internship, UpdateInternshipDTO $dto): ApplicantInternship
    {
        return DB::transaction(function () use ($internship, $dto) {
            $data = array_filter([
                'batch_id'               => $dto->batchId,
                'internship_program_id'  => $dto->internshipProgramId,
                'program_type'           => $dto->programType,
                'status'                 => $dto->status,
                'dispatching_company_id' => $dto->dispatchingCompanyId,
                'accepting_company_id'   => $dto->acceptingCompanyId,
                'receiving_company_id'   => $dto->receivingCompanyId,
                'job_description'        => $dto->jobDescription,
                'place_of_internship'    => $dto->placeOfInternship,
                'municipality'           => $dto->municipality,
                'agreement_date'         => $dto->agreementDate,
                'contract_start'         => $dto->contractStart,
                'contract_end'           => $dto->contractEnd,
                'contract_years'         => $dto->contractYears,
                'stipend_amount'         => $dto->stipendAmount,
                'meal_allowance_amount'  => $dto->mealAllowanceAmount,
                'work_days'              => $dto->workDays,
                'day_off'                => $dto->dayOff,
                'time_start'             => $dto->timeStart,
                'time_end'               => $dto->timeEnd,
                'lunch_break'            => $dto->lunchBreak,
            ], fn ($v) => $v !== null);

            $internship->update($data);

            return $internship->fresh([
                'applicant', 'program', 'receivingCompany', 'acceptingCompany', 'dispatchingCompany', 'batch',
            ]);
        });
    }
}