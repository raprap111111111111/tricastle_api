<?php

namespace App\Domain\Applicant\Mappers;

use App\Domain\Applicant\DTOs\CreateApplicantDTO;
use App\Domain\Applicant\DTOs\UpdateApplicantDTO;
use App\Domain\Applicant\DTOs\UpdateStatusDTO;
use App\Enums\ApplicantStatus;
use App\Http\Requests\v1\Applicant\RejectApplicantRequest;
use App\Http\Requests\v1\Applicant\StoreApplicantRequest;
use App\Http\Requests\v1\Applicant\UpdateApplicantRequest;
use App\Http\Requests\v1\Applicant\UpdateApplicantStatusRequest;

class ApplicantMapper
{
    // ─── existing fromCreateRequest / fromUpdateRequest ───

    public static function fromCreateRequest(StoreApplicantRequest $request): CreateApplicantDTO
    {
        return new CreateApplicantDTO(
            firstName:        $request->validated('first_name'),
            lastName:         $request->validated('last_name'),
            email:            $request->validated('email'),
            middleName:       $request->validated('middle_name'),
            suffix:           $request->validated('suffix'),
            phone:            $request->validated('phone'),
            mobile:           $request->validated('mobile'),
            dateOfBirth:      $request->validated('date_of_birth'),
            gender:           $request->validated('gender'),
            civilStatus:      $request->validated('civil_status'),
            numberOfChildren: $request->validated('number_of_children') ?? 0,
            nationality:      $request->validated('nationality') ?? 'Filipino',
            heightCm:         $request->validated('height_cm'),
            weightKg:         $request->validated('weight_kg'),
            dominantHand:     $request->validated('dominant_hand'),
            bloodType:        $request->validated('blood_type'),
            currentAddress:   $request->validated('current_address'),
            permanentAddress: $request->validated('permanent_address'),
            city:             $request->validated('city'),
            province:         $request->validated('province'),
            postalCode:       $request->validated('postal_code'),
            passportNumber:   $request->validated('passport_number'),
            passportExpiry:   $request->validated('passport_expiry'),
            sssNumber:        $request->validated('sss_number'),
            tinNumber:        $request->validated('tin_number'),
            philhealthNumber: $request->validated('philhealth_number'),
            pagibigNumber:    $request->validated('pagibig_number'),
            assignedStaffId:  $request->validated('assigned_staff_id'),
            createdBy:        $request->user()?->id,
        );
    }

    public static function fromUpdateRequest(UpdateApplicantRequest $request): UpdateApplicantDTO
    {
        return new UpdateApplicantDTO(
            firstName:        $request->validated('first_name'),
            middleName:       $request->validated('middle_name'),
            lastName:         $request->validated('last_name'),
            suffix:           $request->validated('suffix'),
            email:            $request->validated('email'),
            phone:            $request->validated('phone'),
            mobile:           $request->validated('mobile'),
            dateOfBirth:      $request->validated('date_of_birth'),
            gender:           $request->validated('gender'),
            civilStatus:      $request->validated('civil_status'),
            numberOfChildren: $request->validated('number_of_children'),
            nationality:      $request->validated('nationality'),
            heightCm:         $request->validated('height_cm'),
            weightKg:         $request->validated('weight_kg'),
            dominantHand:     $request->validated('dominant_hand'),
            bloodType:        $request->validated('blood_type'),
            currentAddress:   $request->validated('current_address'),
            permanentAddress: $request->validated('permanent_address'),
            city:             $request->validated('city'),
            province:         $request->validated('province'),
            postalCode:       $request->validated('postal_code'),
            passportNumber:   $request->validated('passport_number'),
            passportExpiry:   $request->validated('passport_expiry'),
            sssNumber:        $request->validated('sss_number'),
            tinNumber:        $request->validated('tin_number'),
            philhealthNumber: $request->validated('philhealth_number'),
            pagibigNumber:    $request->validated('pagibig_number'),
            status:           $request->validated('status'),
            rejectionReason:  $request->validated('rejection_reason'),
            qualityScore:     $request->validated('quality_score'),
            qualityGrade:     $request->validated('quality_grade'),
            assignedStaffId:  $request->validated('assigned_staff_id'),
            reviewedBy:       $request->user()?->id,
        );
    }

    // ─── NEW status mappers ──────────────────────────────

    public static function fromUpdateStatusRequest(UpdateApplicantStatusRequest $request): UpdateStatusDTO
    {
        return new UpdateStatusDTO(
            status:          ApplicantStatus::from($request->validated('status')),
            rejectionReason: $request->validated('rejection_reason'),
            reviewedBy:      $request->user()?->id,
        );
    }

    public static function fromRejectRequest(RejectApplicantRequest $request): UpdateStatusDTO
    {
        return new UpdateStatusDTO(
            status:          ApplicantStatus::Rejected,
            rejectionReason: $request->validated('rejection_reason'),
            reviewedBy:      $request->user()?->id,
        );
    }

    public static function forMoveToFinalList(?int $reviewedBy): UpdateStatusDTO
    {
        return new UpdateStatusDTO(
            status:     ApplicantStatus::FinalList,
            reviewedBy: $reviewedBy,
        );
    }
}