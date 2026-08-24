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
    public static function fromCreateRequest(StoreApplicantRequest $request): CreateApplicantDTO
    {
        return new CreateApplicantDTO(
            // ── Personal ──────────────────────────────────────────────────
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

            // ── AIS / Trade Test (NEW) ────────────────────────────────────
            appliedPosition:       $request->validated('applied_position'),
            tradeTestTry:          $request->validated('trade_test_try'),
            tradeTestDate:         $request->validated('trade_test_date'),
            birthplace:            $request->validated('birthplace'),
            religion:              $request->validated('religion'),
            englishProficiencyPct: (int) ($request->validated('english_proficiency_pct') ?? 0),

            // ── Physical ──────────────────────────────────────────────────
            heightCm:         $request->validated('height_cm'),
            weightKg:         $request->validated('weight_kg'),
            dominantHand:     $request->validated('dominant_hand'),
            bloodType:        $request->validated('blood_type'),

            // ── Address ───────────────────────────────────────────────────
            currentAddress:   $request->validated('current_address'),
            permanentAddress: $request->validated('permanent_address'),
            city:             $request->validated('city'),
            province:         $request->validated('province'),
            postalCode:       $request->validated('postal_code'),

            // ── Passport / IDs ────────────────────────────────────────────
            passportNumber:   $request->validated('passport_number'),
            passportExpiry:   $request->validated('passport_expiry'),
            sssNumber:        $request->validated('sss_number'),
            tinNumber:        $request->validated('tin_number'),
            philhealthNumber: $request->validated('philhealth_number'),
            pagibigNumber:    $request->validated('pagibig_number'),

            // ── Skill / Trade ─────────────────────────────────────────────
            skillCategory:           $request->validated('skill_category'),
            tradeOrOccupation:       $request->validated('trade_or_occupation'),

            // ── Language ──────────────────────────────────────────────────
            understandsBasicEnglish: (bool) ($request->validated('understands_basic_english') ?? false),
            jlptLevel:               $request->validated('jlpt_level'),

            // ── Japan Deployment ──────────────────────────────────────────
            willingToBeDeployed:     (bool) ($request->validated('willing_to_be_deployed') ?? false),
            japanDeploymentReady:    (bool) ($request->validated('japan_deployment_ready') ?? false),
            preferredWorkLocation:   $request->validated('preferred_work_location'),

            // ── Japan Experience ──────────────────────────────────────────
            previousJapanExperience: (bool) ($request->validated('previous_japan_experience') ?? false),
            yearsJapanExperience:    (int)  ($request->validated('years_japan_experience') ?? 0),

            // ── Certifications ────────────────────────────────────────────
            hasTitpCertificate:      (bool) ($request->validated('has_titp_certificate') ?? false),
            titpOccupation:          $request->validated('titp_occupation'),
            sswEligible:             (bool) ($request->validated('ssw_eligible') ?? false),

            // ── Salary ────────────────────────────────────────────────────
            expectedSalary:          $request->validated('expected_salary'),
            expectedSalaryCurrency:  $request->validated('expected_salary_currency') ?? 'JPY',
            currentSalary:           $request->validated('current_salary'),
            currentSalaryCurrency:   $request->validated('current_salary_currency') ?? 'PHP',

            // ── Family ────────────────────────────────────────────────────
            fatherName:              $request->validated('father_name'),
            fatherOccupation:        $request->validated('father_occupation'),
            fatherContact:           $request->validated('father_contact'),
            motherName:              $request->validated('mother_name'),
            motherOccupation:        $request->validated('mother_occupation'),
            motherContact:           $request->validated('mother_contact'),
            spouseName:              $request->validated('spouse_name'),
            spouseOccupation:        $request->validated('spouse_occupation'),
            spouseContact:           $request->validated('spouse_contact'),
            spouseSalary:            $request->validated('spouse_salary'),
            spouseSalaryUnit:        $request->validated('spouse_salary_unit') ?? 'per_month',

            // ── Japan Contacts ────────────────────────────────────────────
            japanContacts:           $request->validated('japan_contacts') ?? [],

            // ── Emergency Contact ─────────────────────────────────────────
            emergencyContactName:         $request->validated('emergency_contact_name'),
            emergencyContactRelationship: $request->validated('emergency_contact_relationship'),
            emergencyContactPhone:        $request->validated('emergency_contact_phone'),
            emergencyContactAddress:      $request->validated('emergency_contact_address'),

            // ── Staff ─────────────────────────────────────────────────────
            assignedStaffId:  $request->validated('assigned_staff_id'),
            createdBy:        $request->user()?->id,
        );
    }

    public static function fromUpdateRequest(UpdateApplicantRequest $request): UpdateApplicantDTO
    {
        return new UpdateApplicantDTO(
            // ── Personal ──────────────────────────────────────────────────
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

            // ── AIS / Trade Test (NEW) ────────────────────────────────────
            appliedPosition:       $request->validated('applied_position'),
            tradeTestTry:          $request->validated('trade_test_try'),
            tradeTestDate:         $request->validated('trade_test_date'),
            birthplace:            $request->validated('birthplace'),
            religion:              $request->validated('religion'),
            englishProficiencyPct: $request->hasInput('english_proficiency_pct') 
                                     ? (int) $request->validated('english_proficiency_pct') 
                                     : null,

            // ── Physical ──────────────────────────────────────────────────
            heightCm:         $request->validated('height_cm'),
            weightKg:         $request->validated('weight_kg'),
            dominantHand:     $request->validated('dominant_hand'),
            bloodType:        $request->validated('blood_type'),

            // ── Address ───────────────────────────────────────────────────
            currentAddress:   $request->validated('current_address'),
            permanentAddress: $request->validated('permanent_address'),
            city:             $request->validated('city'),
            province:         $request->validated('province'),
            postalCode:       $request->validated('postal_code'),

            // ── Passport / IDs ────────────────────────────────────────────
            passportNumber:   $request->validated('passport_number'),
            passportExpiry:   $request->validated('passport_expiry'),
            sssNumber:        $request->validated('sss_number'),
            tinNumber:        $request->validated('tin_number'),
            philhealthNumber: $request->validated('philhealth_number'),
            pagibigNumber:    $request->validated('pagibig_number'),

            // ── Skill / Trade ─────────────────────────────────────────────
            skillCategory:           $request->validated('skill_category'),
            tradeOrOccupation:       $request->validated('trade_or_occupation'),

            // ── Language ──────────────────────────────────────────────────
            understandsBasicEnglish: $request->hasInput('understands_basic_english')
                                         ? (bool) $request->validated('understands_basic_english')
                                         : null,
            jlptLevel:               $request->validated('jlpt_level'),

            // ── Japan Deployment ──────────────────────────────────────────
            willingToBeDeployed:     $request->hasInput('willing_to_be_deployed')
                                         ? (bool) $request->validated('willing_to_be_deployed')
                                         : null,
            japanDeploymentReady:    $request->hasInput('japan_deployment_ready')
                                         ? (bool) $request->validated('japan_deployment_ready')
                                         : null,
            preferredWorkLocation:   $request->validated('preferred_work_location'),

            // ── Japan Experience ──────────────────────────────────────────
            previousJapanExperience: $request->hasInput('previous_japan_experience')
                                         ? (bool) $request->validated('previous_japan_experience')
                                         : null,
            yearsJapanExperience:    $request->validated('years_japan_experience') !== null
                                         ? (int) $request->validated('years_japan_experience')
                                         : null,

            // ── Certifications ────────────────────────────────────────────
            hasTitpCertificate:      $request->hasInput('has_titp_certificate')
                                         ? (bool) $request->validated('has_titp_certificate')
                                         : null,
            titpOccupation:          $request->validated('titp_occupation'),
            sswEligible:             $request->hasInput('ssw_eligible')
                                         ? (bool) $request->validated('ssw_eligible')
                                         : null,

            // ── Salary ────────────────────────────────────────────────────
            expectedSalary:         $request->validated('expected_salary'),
            expectedSalaryCurrency: $request->validated('expected_salary_currency'),
            currentSalary:          $request->validated('current_salary'),
            currentSalaryCurrency:  $request->validated('current_salary_currency'),

            // ── Family ────────────────────────────────────────────────────
            fatherName:              $request->validated('father_name'),
            fatherOccupation:        $request->validated('father_occupation'),
            fatherContact:           $request->validated('father_contact'),
            motherName:              $request->validated('mother_name'),
            motherOccupation:        $request->validated('mother_occupation'),
            motherContact:           $request->validated('mother_contact'),
            spouseName:              $request->validated('spouse_name'),
            spouseOccupation:        $request->validated('spouse_occupation'),
            spouseContact:           $request->validated('spouse_contact'),
            spouseSalary:            $request->validated('spouse_salary'),
            spouseSalaryUnit:        $request->validated('spouse_salary_unit'),

            // ── Japan Contacts ────────────────────────────────────────────
            japanContacts:           $request->validated('japan_contacts'),

            // ── Emergency Contact ─────────────────────────────────────────
            emergencyContactName:         $request->validated('emergency_contact_name'),
            emergencyContactRelationship: $request->validated('emergency_contact_relationship'),
            emergencyContactPhone:        $request->validated('emergency_contact_phone'),
            emergencyContactAddress:      $request->validated('emergency_contact_address'),

            // ── Status ────────────────────────────────────────────────────
            status:          $request->validated('status'),
            rejectionReason: $request->validated('rejection_reason'),

            // ── Quality ───────────────────────────────────────────────────
            qualityScore:    $request->validated('quality_score'),
            qualityGrade:    $request->validated('quality_grade'),

            // ── Staff ─────────────────────────────────────────────────────
            assignedStaffId: $request->validated('assigned_staff_id'),
            reviewedBy:      $request->user()?->id,
        );
    }

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