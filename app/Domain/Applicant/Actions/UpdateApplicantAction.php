<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\ActivityLog\Traits\LogsActivity;
use App\Domain\Applicant\DTOs\UpdateApplicantDTO;
use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Applicant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateApplicantAction
{
    use LogsActivity;
    use HasNotifications;

    public function __construct(
        private readonly ApplicantRepository $repository
    ) {}

    public function execute(Applicant $applicant, UpdateApplicantDTO $dto): Applicant
    {
        $updated = DB::transaction(function () use ($applicant, $dto) {

            // ── 1. Filter out null values for partial updates ────────────────
            $data = array_filter([
                // ── Personal ─────────────────────────────────────────────────
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

                // ── AIS / Trade Test ─────────────────────────────────────────
                'applied_position'        => $dto->appliedPosition,
                'trade_test_try'          => $dto->tradeTestTry,
                'trade_test_date'         => $dto->tradeTestDate,
                'birthplace'              => $dto->birthplace,
                'religion'                => $dto->religion,
                'english_proficiency_pct' => $dto->englishProficiencyPct,

                // ── Physical ─────────────────────────────────────────────────
                'height_cm'          => $dto->heightCm,
                'weight_kg'          => $dto->weightKg,
                'dominant_hand'      => $dto->dominantHand,
                'blood_type'         => $dto->bloodType,

                // ── Address ──────────────────────────────────────────────────
                'current_address'    => $dto->currentAddress,
                'permanent_address'  => $dto->permanentAddress,
                'city'               => $dto->city,
                'province'           => $dto->province,
                'postal_code'        => $dto->postalCode,

                // ── Passport / IDs ───────────────────────────────────────────
                'passport_number'    => $dto->passportNumber,
                'passport_expiry'    => $dto->passportExpiry,
                'sss_number'         => $dto->sssNumber,
                'tin_number'         => $dto->tinNumber,
                'philhealth_number'  => $dto->philhealthNumber,
                'pagibig_number'     => $dto->pagibigNumber,

                // ── Skill / Trade & Language ─────────────────────────────────
                'skill_category'            => $dto->skillCategory,
                'trade_or_occupation'       => $dto->tradeOrOccupation,
                'understands_basic_english' => $dto->understandsBasicEnglish,
                'jlpt_level'                => $dto->jlptLevel,

                // ── Japan Deployment Readiness ───────────────────────────────
                'willing_to_be_deployed'    => $dto->willingToBeDeployed,
                'japan_deployment_ready'    => $dto->japanDeploymentReady,
                'preferred_work_location'   => $dto->preferredWorkLocation,
                'previous_japan_experience' => $dto->previousJapanExperience,
                'years_japan_experience'    => $dto->yearsJapanExperience,
                'has_titp_certificate'      => $dto->hasTitpCertificate,
                'titp_occupation'           => $dto->titpOccupation,
                'ssw_eligible'              => $dto->sswEligible,

                // ── Salary ───────────────────────────────────────────────────
                'expected_salary'           => $dto->expectedSalary,
                'expected_salary_currency'  => $dto->expectedSalaryCurrency,
                'current_salary'            => $dto->currentSalary,
                'current_salary_currency'   => $dto->currentSalaryCurrency,

                // ── Legacy Family ────────────────────────────────────────────
                'father_name'               => $dto->fatherName,
                'father_occupation'         => $dto->fatherOccupation,
                'father_contact'            => $dto->fatherContact,
                'mother_name'               => $dto->motherName,
                'mother_occupation'         => $dto->motherOccupation,
                'mother_contact'            => $dto->motherContact,
                'spouse_name'               => $dto->spouseName,
                'spouse_occupation'         => $dto->spouseOccupation,
                'spouse_contact'            => $dto->spouseContact,

                // ── Emergency Contact ────────────────────────────────────────
                'emergency_contact_name'         => $dto->emergencyContactName,
                'emergency_contact_relationship' => $dto->emergencyContactRelationship,
                'emergency_contact_phone'        => $dto->emergencyContactPhone,
                'emergency_contact_address'      => $dto->emergencyContactAddress,

                // ── Quality ──────────────────────────────────────────────────
                'quality_score'      => $dto->qualityScore,
                'quality_grade'      => $dto->qualityGrade,

                // ── Staff ────────────────────────────────────────────────────
                'assigned_staff_id'  => $dto->assignedStaffId,
                'reviewed_by'        => $dto->reviewedBy,
            ], fn($value) => $value !== null);

            // ── 2. Handle status transitions ─────────────────────────────────
            if ($dto->status !== null) {
                $data['status'] = $dto->status;

                if ($dto->status === 'final_list') {
                    $data['final_listed_at']  = now();
                    $data['rejected_at']      = null;
                    $data['rejection_reason'] = null;

                    Log::info('Applicant moved to final list', [
                        'applicant_id' => $applicant->id,
                        'reviewed_by'  => $dto->reviewedBy,
                    ]);
                }

                if ($dto->status === 'rejected') {
                    $data['rejected_at']      = now();
                    $data['rejection_reason'] = $dto->rejectionReason;
                    $data['final_listed_at']  = null;

                    Log::info('Applicant rejected', [
                        'applicant_id'     => $applicant->id,
                        'rejection_reason' => $dto->rejectionReason,
                        'reviewed_by'      => $dto->reviewedBy,
                    ]);
                }
            }

            // ── 3. Update main Applicant record ──────────────────────────────
            if (!empty($data)) {
                $this->repository->update($applicant->id, $data);
            }

            // ── 4. Sync Relational Family Record ─────────────────────────────
            if ($dto->spouseName !== null || $dto->fatherName !== null || $dto->motherName !== null || $dto->spouseSalary !== null) {
                $applicant->family()->updateOrCreate(
                    ['applicant_id' => $applicant->id],
                    array_filter([
                        'spouse_name'        => $dto->spouseName,
                        'spouse_occupation'  => $dto->spouseOccupation,
                        'spouse_salary'      => $dto->spouseSalary,
                        'spouse_salary_unit' => $dto->spouseSalaryUnit ?? 'per_month',
                        'father_name'        => $dto->fatherName,
                        'mother_name'        => $dto->motherName,
                    ], fn($val) => $val !== null)
                );
            }

            // ── 5. Sync Japan Contacts ───────────────────────────────────────
            if (is_array($dto->japanContacts)) {
                $applicant->japanContacts()->delete();
                if (!empty($dto->japanContacts)) {
                    $applicant->japanContacts()->createMany($dto->japanContacts);
                }
            }

            Log::info('Applicant updated', [
                'applicant_id' => $applicant->id,
                'updated_by'   => $dto->reviewedBy,
            ]);

            return $applicant->fresh([
                'family',
                'japanContacts',
                'assignedStaff',
                'reviewer',
                'creator',
                'lifestyle',
                'educations',
                'employments',
                'tattoos',
            ]);
        });

        // ── 6. Send Notification ─────────────────────────────────────────────
        $name = "{$updated->first_name} {$updated->last_name}";
        $code = $updated->applicant_code;

        if ($updated->assigned_staff_id) {
            $this->notifyUser(
                user: $updated->assigned_staff_id,
                title: '✏️ Applicant Updated',
                message: "{$name} ({$code}) has been updated.",
                module: 'applicant',
                actionUrl: "/applicants/{$updated->id}",
            );
        }

        return $updated;
    }
}