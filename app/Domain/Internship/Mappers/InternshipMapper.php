<?php

namespace App\Domain\Internship\Mappers;

use App\Domain\Internship\DTOs\ChangeCompanyDTO;
use App\Domain\Internship\DTOs\CreateInternshipDTO;
use App\Domain\Internship\DTOs\GenerateMoaDTO;
use App\Domain\Internship\DTOs\SyncGuarantorsDTO;
use App\Domain\Internship\DTOs\UpdateInternshipDTO;
use App\Http\Requests\v1\Internship\BulkGenerateMoaRequest;
use App\Http\Requests\v1\Internship\ChangeCompanyRequest;
use App\Http\Requests\v1\Internship\GenerateMoaRequest;
use App\Http\Requests\v1\Internship\StoreInternshipRequest;
use App\Http\Requests\v1\Internship\SyncGuarantorsRequest;
use App\Http\Requests\v1\Internship\UpdateInternshipRequest;

class InternshipMapper
{
    public static function fromCreateRequest(StoreInternshipRequest $request): CreateInternshipDTO
    {
        return new CreateInternshipDTO(
            applicantId:           (int) $request->validated('applicant_id'),
            batchId:               $request->validated('batch_id'),
            internshipProgramId:   $request->validated('internship_program_id'),
            programType:           $request->validated('program_type') ?? 'titp',
            dispatchingCompanyId:  $request->validated('dispatching_company_id'),
            acceptingCompanyId:    $request->validated('accepting_company_id'),
            receivingCompanyId:    $request->validated('receiving_company_id'),
            jobDescription:        $request->validated('job_description'),
            placeOfInternship:     $request->validated('place_of_internship'),
            municipality:          $request->validated('municipality'),
            agreementDate:         $request->validated('agreement_date'),
            contractStart:         $request->validated('contract_start'),
            contractEnd:           $request->validated('contract_end'),
            contractYears:         $request->validated('contract_years'),
            stipendAmount:         $request->validated('stipend_amount'),
            mealAllowanceAmount:   $request->validated('meal_allowance_amount'),
            workDays:              $request->validated('work_days'),
            dayOff:                $request->validated('day_off'),
            timeStart:             $request->validated('time_start'),
            timeEnd:               $request->validated('time_end'),
            lunchBreak:            $request->validated('lunch_break'),
            guarantors:            $request->validated('guarantors'),
            createdBy:             $request->user()?->id,
        );
    }

    public static function fromUpdateRequest(UpdateInternshipRequest $request): UpdateInternshipDTO
    {
        return new UpdateInternshipDTO(
            batchId:               $request->validated('batch_id'),
            internshipProgramId:   $request->validated('internship_program_id'),
            programType:           $request->validated('program_type'),
            status:                $request->validated('status'),
            dispatchingCompanyId:  $request->validated('dispatching_company_id'),
            acceptingCompanyId:    $request->validated('accepting_company_id'),
            receivingCompanyId:    $request->validated('receiving_company_id'),
            jobDescription:        $request->validated('job_description'),
            placeOfInternship:     $request->validated('place_of_internship'),
            municipality:          $request->validated('municipality'),
            agreementDate:         $request->validated('agreement_date'),
            contractStart:         $request->validated('contract_start'),
            contractEnd:           $request->validated('contract_end'),
            contractYears:         $request->validated('contract_years'),
            stipendAmount:         $request->validated('stipend_amount'),
            mealAllowanceAmount:   $request->validated('meal_allowance_amount'),
            workDays:              $request->validated('work_days'),
            dayOff:                $request->validated('day_off'),
            timeStart:             $request->validated('time_start'),
            timeEnd:               $request->validated('time_end'),
            lunchBreak:            $request->validated('lunch_break'),
            updatedBy:             $request->user()?->id,
        );
    }

    public static function fromChangeCompanyRequest(ChangeCompanyRequest $request): ChangeCompanyDTO
    {
        return new ChangeCompanyDTO(
            receivingCompanyId:   (int) $request->validated('receiving_company_id'),
            contractStart:        $request->validated('contract_start'),
            changeReason:         $request->validated('change_reason'),
            acceptingCompanyId:   $request->validated('accepting_company_id'),
            batchId:              $request->validated('batch_id'),
            internshipProgramId:  $request->validated('internship_program_id'),
            contractEnd:          $request->validated('contract_end'),
            contractYears:        $request->validated('contract_years'),
            jobDescription:       $request->validated('job_description'),
            placeOfInternship:    $request->validated('place_of_internship'),
            stipendAmount:        $request->validated('stipend_amount'),
            mealAllowanceAmount:  $request->validated('meal_allowance_amount'),
            changedBy:            $request->user()?->id,
        );
    }

    public static function fromGenerateMoaRequest(GenerateMoaRequest $request): GenerateMoaDTO
    {
        return new GenerateMoaDTO(
            agreementDate:        $request->validated('agreement_date'),
            municipality:         $request->validated('municipality'),
            contractStart:        $request->validated('contract_start'),
            contractEnd:          $request->validated('contract_end'),
            contractYears:        $request->validated('contract_years'),
            jobDescription:       $request->validated('job_description'),
            placeOfInternship:    $request->validated('place_of_internship'),
            stipendAmount:        $request->validated('stipend_amount'),
            mealAllowanceAmount:  $request->validated('meal_allowance_amount'),
            generatedBy:          $request->user()?->id,
        );
    }

    public static function fromBulkGenerateMoaRequest(BulkGenerateMoaRequest $request): GenerateMoaDTO
    {
        return new GenerateMoaDTO(
            agreementDate:        $request->validated('agreement_date'),
            municipality:         $request->validated('municipality'),
            contractStart:        $request->validated('contract_start'),
            contractEnd:          $request->validated('contract_end'),
            contractYears:        $request->validated('contract_years'),
            jobDescription:       $request->validated('job_description'),
            placeOfInternship:    $request->validated('place_of_internship'),
            stipendAmount:        $request->validated('stipend_amount'),
            mealAllowanceAmount:  $request->validated('meal_allowance_amount'),
            generatedBy:          $request->user()?->id,
        );
    }

    public static function fromSyncGuarantorsRequest(SyncGuarantorsRequest $request, int $applicantId): SyncGuarantorsDTO
    {
        return new SyncGuarantorsDTO(
            applicantId: $applicantId,
            guarantors:  $request->validated('guarantors'),
        );
    }
}