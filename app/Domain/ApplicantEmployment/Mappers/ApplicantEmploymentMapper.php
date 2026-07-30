<?php

// app/Domain/ApplicantEmployment/Mappers/ApplicantEmploymentMapper.php

namespace App\Domain\ApplicantEmployment\Mappers;

use App\Domain\ApplicantEmployment\DTOs\CreateApplicantEmploymentDTO;
use App\Domain\ApplicantEmployment\DTOs\UpdateApplicantEmploymentDTO;
use App\Http\Requests\v1\ApplicantEmployment\StoreApplicantEmploymentRequest;
use App\Http\Requests\v1\ApplicantEmployment\UpdateApplicantEmploymentRequest;

class ApplicantEmploymentMapper
{
    public static function fromStoreRequest(StoreApplicantEmploymentRequest $request): CreateApplicantEmploymentDTO
    {
        return new CreateApplicantEmploymentDTO(
            applicantId:      (int) $request->validated('applicant_id'),
            companyName:      $request->validated('company_name'),
            position:         $request->validated('position'),
            dateStarted:      $request->validated('date_started'),
            industry:         $request->validated('industry'),
            jobDescription:   $request->validated('job_description'),
            dateEnded:        $request->validated('date_ended'),
            isCurrent:        (bool) $request->validated('is_current', false),
            country:          $request->validated('country', 'Philippines'),
            city:             $request->validated('city'),
            salary:           $request->validated('salary') !== null
                                ? (float) $request->validated('salary')
                                : null,
            salaryCurrency:   $request->validated('salary_currency', 'PHP'),
            reasonForLeaving: $request->validated('reason_for_leaving'),
        );
    }

    public static function fromUpdateRequest(UpdateApplicantEmploymentRequest $request): UpdateApplicantEmploymentDTO
    {
        return new UpdateApplicantEmploymentDTO(
            companyName:      $request->validated('company_name'),
            position:         $request->validated('position'),
            industry:         $request->validated('industry'),
            jobDescription:   $request->validated('job_description'),
            dateStarted:      $request->validated('date_started'),
            dateEnded:        $request->validated('date_ended'),
            isCurrent:        $request->has('is_current')
                                ? (bool) $request->validated('is_current')
                                : null,
            country:          $request->validated('country'),
            city:             $request->validated('city'),
            salary:           $request->validated('salary') !== null
                                ? (float) $request->validated('salary')
                                : null,
            salaryCurrency:   $request->validated('salary_currency'),
            reasonForLeaving: $request->validated('reason_for_leaving'),
        );
    }
}