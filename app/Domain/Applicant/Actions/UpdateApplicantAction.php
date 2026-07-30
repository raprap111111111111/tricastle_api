<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\DTOs\UpdateApplicantDTO;
use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Models\Applicant;

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

            'status'             => $dto->status,
            'quality_score'      => $dto->qualityScore,
            'quality_grade'      => $dto->qualityGrade,
            'assigned_staff_id'  => $dto->assignedStaffId,
        ], fn ($value) => $value !== null);

        return $this->repository->update($applicant->id, $data);
    }
}