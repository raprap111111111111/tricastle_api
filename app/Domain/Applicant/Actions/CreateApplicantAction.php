<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\ActivityLog\Traits\LogsActivity;
use App\Domain\Applicant\DTOs\CreateApplicantDTO;
use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Domain\Applicant\Services\DuplicateDetectionService;
use App\Domain\Notification\Traits\HasNotifications;
use App\Exceptions\DuplicateApplicantException;
use App\Models\Applicant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateApplicantAction
{
    use LogsActivity;
    use HasNotifications;   // 🔔 One-line setup

    public function __construct(
        private readonly ApplicantRepository       $repository,
        private readonly DuplicateDetectionService $duplicateService,
    ) {}

    public function execute(CreateApplicantDTO $dto): Applicant
    {
        $applicant = DB::transaction(function () use ($dto) {

            // ── 1. Duplicate detection ──────────────────────
            $duplicates = $this->duplicateService->check(
                data: [
                    'email'           => $dto->email,
                    'first_name'      => $dto->firstName,
                    'middle_name'     => $dto->middleName,
                    'last_name'       => $dto->lastName,
                    'date_of_birth'   => $dto->dateOfBirth,
                    'passport_number' => $dto->passportNumber,
                ],
            );

            if ($this->duplicateService->hasBlockers($duplicates)) {
                $blockers = $this->duplicateService->getBlockers($duplicates);

                Log::warning('Duplicate applicant creation blocked', [
                    'email' => $dto->email,
                    'name'  => "{$dto->firstName} {$dto->lastName}",
                ]);

                throw new DuplicateApplicantException(
                    duplicates: $blockers,
                    message: $blockers[0]['message'],
                );
            }

            // ── 2. Create applicant (always starts as pending) ──
            // Replace the repository->create([...]) block inside execute()

            $applicant = $this->repository->create([
                // ── Personal ──────────────────────────────────────────────────────
                'first_name'         => $dto->firstName,
                'middle_name'        => $dto->middleName,
                'last_name'          => $dto->lastName,
                'suffix'             => $dto->suffix,
                'email'              => $dto->email,
                'phone'              => $dto->phone,
                'mobile'             => $dto->mobile,
                'date_of_birth'      => $dto->dateOfBirth,
                'gender'             => $dto->gender,
                'civil_status'       => $dto->civilStatus,
                'number_of_children' => $dto->numberOfChildren,
                'nationality'        => $dto->nationality,

                // ── Physical ──────────────────────────────────────────────────────
                'height_cm'          => $dto->heightCm,
                'weight_kg'          => $dto->weightKg,
                'dominant_hand'      => $dto->dominantHand,
                'blood_type'         => $dto->bloodType,

                // ── Address ───────────────────────────────────────────────────────
                'current_address'    => $dto->currentAddress,
                'permanent_address'  => $dto->permanentAddress,
                'city'               => $dto->city,
                'province'           => $dto->province,
                'postal_code'        => $dto->postalCode,

                // ── Passport / IDs ────────────────────────────────────────────────
                'passport_number'    => $dto->passportNumber,
                'passport_expiry'    => $dto->passportExpiry,
                'sss_number'         => $dto->sssNumber,
                'tin_number'         => $dto->tinNumber,
                'philhealth_number'  => $dto->philhealthNumber,
                'pagibig_number'     => $dto->pagibigNumber,

                // ── Skill / Trade ─────────────────────────────────────────────────
                'skill_category'             => $dto->skillCategory,
                'trade_or_occupation'        => $dto->tradeOrOccupation,

                // ── Language ──────────────────────────────────────────────────────
                'understands_basic_english'  => $dto->understandsBasicEnglish,
                'jlpt_level'                 => $dto->jlptLevel,

                // ── Japan Deployment ──────────────────────────────────────────────
                'willing_to_be_deployed'     => $dto->willingToBeDeployed,
                'japan_deployment_ready'     => $dto->japanDeploymentReady,
                'preferred_work_location'    => $dto->preferredWorkLocation,

                // ── Japan Experience ──────────────────────────────────────────────
                'previous_japan_experience'  => $dto->previousJapanExperience,
                'years_japan_experience'     => $dto->yearsJapanExperience,

                // ── Certifications ────────────────────────────────────────────────
                'has_titp_certificate'       => $dto->hasTitpCertificate,
                'titp_occupation'            => $dto->titpOccupation,
                'ssw_eligible'               => $dto->sswEligible,

                // ── Salary ────────────────────────────────────────────────────────
                'expected_salary'            => $dto->expectedSalary,
                'expected_salary_currency'   => $dto->expectedSalaryCurrency,
                'current_salary'             => $dto->currentSalary,
                'current_salary_currency'    => $dto->currentSalaryCurrency,

                // ── Family ────────────────────────────────────────────────────────
                'father_name'                => $dto->fatherName,
                'father_occupation'          => $dto->fatherOccupation,
                'father_contact'             => $dto->fatherContact,
                'mother_name'                => $dto->motherName,
                'mother_occupation'          => $dto->motherOccupation,
                'mother_contact'             => $dto->motherContact,
                'spouse_name'                => $dto->spouseName,
                'spouse_occupation'          => $dto->spouseOccupation,
                'spouse_contact'             => $dto->spouseContact,

                // ── Emergency Contact ─────────────────────────────────────────────
                'emergency_contact_name'         => $dto->emergencyContactName,
                'emergency_contact_relationship' => $dto->emergencyContactRelationship,
                'emergency_contact_phone'        => $dto->emergencyContactPhone,
                'emergency_contact_address'      => $dto->emergencyContactAddress,

                // ── Staff / Meta ──────────────────────────────────────────────────
                'assigned_staff_id'  => $dto->assignedStaffId,
                'created_by'         => $dto->createdBy,
                'status'             => 'pending',
            ]);

            Log::info('Applicant created', [
                'applicant_id' => $applicant->id,
                'email'        => $applicant->email,
                'created_by'   => $dto->createdBy,
            ]);

            return $applicant;
        });

        // 🔔 Notifications AFTER transaction commits
        $name = "{$applicant->first_name} {$applicant->last_name}";
        $code = $applicant->applicant_code;

        // Notify staff who can view applicants
        $this->notifyStaff(
            permissions: 'applicant.viewAny',
            title: '👤 New Applicant Registered',
            message: "{$name} ({$code}) has been added to the system.",
            module: 'applicant',
            actionUrl: "/applicants/{$applicant->id}",
        );

        // Personal notification to assigned staff
        if ($dto->assignedStaffId && $dto->assignedStaffId !== $dto->createdBy) {
            $this->notifyUser(
                user: $dto->assignedStaffId,
                title: '📋 New Applicant Assigned to You',
                message: "You have been assigned to review {$name} ({$code}).",
                module: 'applicant',
                actionUrl: "/applicants/{$applicant->id}",
            );
        }

        return $applicant;
    }
}
