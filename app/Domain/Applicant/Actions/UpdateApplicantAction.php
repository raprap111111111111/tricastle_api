<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\DTOs\UpdateApplicantDTO;
use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Models\Applicant;
use Illuminate\Support\Facades\Log;

class UpdateApplicantAction
{
    public function __construct(
        private readonly ApplicantRepository $repository
    ) {}

    public function execute(Applicant $applicant, UpdateApplicantDTO $dto): Applicant
    {
        $data = array_filter([
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

            'height_cm'          => $dto->heightCm,
            'weight_kg'          => $dto->weightKg,
            'dominant_hand'      => $dto->dominantHand,
            'blood_type'         => $dto->bloodType,

            'current_address'    => $dto->currentAddress,
            'permanent_address'  => $dto->permanentAddress,
            'city'               => $dto->city,
            'province'           => $dto->province,
            'postal_code'        => $dto->postalCode,

            'passport_number'    => $dto->passportNumber,
            'passport_expiry'    => $dto->passportExpiry,
            'sss_number'         => $dto->sssNumber,
            'tin_number'         => $dto->tinNumber,
            'philhealth_number'  => $dto->philhealthNumber,
            'pagibig_number'     => $dto->pagibigNumber,

            'quality_score'      => $dto->qualityScore,
            'quality_grade'      => $dto->qualityGrade,
            'assigned_staff_id'  => $dto->assignedStaffId,
            'reviewed_by'        => $dto->reviewedBy,
        ], fn($value) => $value !== null);

        // ─── Handle status transitions ────────────────────
        if ($dto->status !== null) {
            $data['status'] = $dto->status;

            // Moving to final list
            if ($dto->status === 'final_list') {
                $data['final_listed_at'] = now();
                $data['rejected_at']     = null;
                $data['rejection_reason'] = null;

                Log::info('Applicant moved to final list', [
                    'applicant_id' => $applicant->id,
                    'reviewed_by'  => $dto->reviewedBy,
                ]);
            }

            // Rejecting applicant
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

        return $this->repository->update($applicant->id, $data);
    }
}