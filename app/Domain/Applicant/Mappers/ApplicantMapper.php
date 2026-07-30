<?php

namespace App\Domain\Applicant\Mappers;

use App\Domain\Applicant\DTOs\BatchAssignmentDTO;
use App\Domain\Applicant\DTOs\CreateApplicantDTO;
use App\Domain\Applicant\DTOs\UpdateApplicantDTO;
use App\Enums\ApplicantBatchStatus;
use App\Http\Requests\v1\Applicant\StoreApplicantRequest;
use App\Http\Requests\v1\Applicant\UpdateApplicantRequest;

class ApplicantMapper
{
    public static function fromCreateRequest(StoreApplicantRequest $request): CreateApplicantDTO
    {
        return new CreateApplicantDTO(
            // Personal (required)
            firstName:        $request->validated('first_name'),
            lastName:         $request->validated('last_name'),
            email:            $request->validated('email'),

            // Personal (optional)
            middleName:       $request->validated('middle_name'),
            suffix:           $request->validated('suffix'),
            phone:            $request->validated('phone'),
            mobile:           $request->validated('mobile'),
            dateOfBirth:      $request->validated('date_of_birth'),
            gender:           $request->validated('gender'),
            civilStatus:      $request->validated('civil_status'),
            numberOfChildren: $request->validated('number_of_children') ?? 0,
            nationality:      $request->validated('nationality') ?? 'Filipino',

            // Physical
            heightCm:         $request->validated('height_cm'),
            weightKg:         $request->validated('weight_kg'),
            dominantHand:     $request->validated('dominant_hand'),
            bloodType:        $request->validated('blood_type'),

            // Address
            currentAddress:   $request->validated('current_address'),
            permanentAddress: $request->validated('permanent_address'),
            city:             $request->validated('city'),
            province:         $request->validated('province'),
            postalCode:       $request->validated('postal_code'),

            // Passport / IDs
            passportNumber:   $request->validated('passport_number'),
            passportExpiry:   $request->validated('passport_expiry'),
            sssNumber:        $request->validated('sss_number'),
            tinNumber:        $request->validated('tin_number'),
            philhealthNumber: $request->validated('philhealth_number'),
            pagibigNumber:    $request->validated('pagibig_number'),

            // Staff
            assignedStaffId:  $request->validated('assigned_staff_id'),
            createdBy:        $request->user()?->id,

            // Batch — build DTO if manual batch_id OR only status provided
            batch: self::buildBatchDto($request),
        );
    }

    /**
     * Build batch DTO from request.
     *
     * - batch_id provided → manual assignment
     * - batch_id NOT provided → still create DTO so status can be used with auto-batch
     * - Neither → null (no batch info at all)
     */
    private static function buildBatchDto(StoreApplicantRequest $request): ?BatchAssignmentDTO
    {
        $hasBatchId     = $request->filled('batch_id');
        $hasBatchStatus = $request->filled('batch_status');

        // Nothing provided → let action auto-resolve completely
        if (!$hasBatchId && !$hasBatchStatus) {
            return null;
        }

        return new BatchAssignmentDTO(
            batchId:     $hasBatchId ? (int) $request->validated('batch_id') : 0,
            status:      self::normalizeStatus($request->validated('batch_status')),
            processedBy: $request->user()?->id,
        );
    }

    /**
     * Normalize batch status → always returns a plain string.
     * Safe for null, string, or enum input.
     */
    private static function normalizeStatus(mixed $status): string
    {
        if ($status === null || $status === '') {
            return ApplicantBatchStatus::APPLIED->value;
        }

        if ($status instanceof ApplicantBatchStatus) {
            return $status->value;
        }

        return (string) $status;
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
            qualityScore:     $request->validated('quality_score'),
            qualityGrade:     $request->validated('quality_grade'),
            assignedStaffId:  $request->validated('assigned_staff_id'),
        );
    }
}