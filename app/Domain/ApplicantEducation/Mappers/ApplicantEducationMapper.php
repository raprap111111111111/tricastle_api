<?php

// app/Domain/ApplicantEducation/Mappers/ApplicantEducationMapper.php

namespace App\Domain\ApplicantEducation\Mappers;

use App\Domain\ApplicantEducation\DTOs\CreateApplicantEducationDTO;
use App\Domain\ApplicantEducation\DTOs\UpdateApplicantEducationDTO;
use App\Http\Requests\v1\ApplicantEducation\StoreApplicantEducationRequest;
use App\Http\Requests\v1\ApplicantEducation\UpdateApplicantEducationRequest;

class ApplicantEducationMapper
{
    public static function fromStoreRequest(StoreApplicantEducationRequest $request): CreateApplicantEducationDTO
    {
        return new CreateApplicantEducationDTO(
            applicantId:     (int) $request->validated('applicant_id'),
            educationLevel:  $request->validated('education_level'),
            schoolName:      $request->validated('school_name'),
            educationStatus: $request->validated('education_status', 'graduate'),
            course:          $request->validated('course'),
            yearStarted:     $request->validated('year_started') !== null
                                ? (int) $request->validated('year_started')
                                : null,
            yearEnded:       $request->validated('year_ended') !== null
                                ? (int) $request->validated('year_ended')
                                : null,
            honors:          $request->validated('honors'),
        );
    }

    public static function fromUpdateRequest(UpdateApplicantEducationRequest $request): UpdateApplicantEducationDTO
    {
        return new UpdateApplicantEducationDTO(
            educationLevel:  $request->validated('education_level'),
            educationStatus: $request->validated('education_status'),
            schoolName:      $request->validated('school_name'),
            course:          $request->validated('course'),
            yearStarted:     $request->validated('year_started') !== null
                                ? (int) $request->validated('year_started')
                                : null,
            yearEnded:       $request->validated('year_ended') !== null
                                ? (int) $request->validated('year_ended')
                                : null,
            honors:          $request->validated('honors'),
        );
    }
}