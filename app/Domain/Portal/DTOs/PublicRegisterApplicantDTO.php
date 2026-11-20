<?php

namespace App\Domain\Portal\DTOs;

use App\Enums\CivilStatus;
use App\Http\Requests\v1\Portal\PublicRegisterApplicantRequest;

class PublicRegisterApplicantDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $dateOfBirth,
        public string $email,
        public string $password,
        public ?string $middleName = null,
        public ?string $suffix = null,
        public ?string $phone = null,
        public ?string $mobile = null,
        public ?string $gender = null,
        public ?CivilStatus $civilStatus = null,
        public ?string $currentAddress = null,
        public ?string $city = null,
        public ?string $province = null,
        public ?string $appliedPosition = null,
        public ?string $skillCategory = null,
        public ?string $tradeOrOccupation = null,
        public ?string $jlptLevel = null,
        public ?bool $willingToBeDeployed = true,
        public ?bool $previousJapanExperience = false,
        public ?int $yearsJapanExperience = 0,
        public ?string $emergencyContactName = null,
        public ?string $emergencyContactRelationship = null,
        public ?string $emergencyContactPhone = null,
    ) {}

    public static function fromRequest(PublicRegisterApplicantRequest $request): self
    {
        $v = $request->validated();

        $civilStatusEnum = null;
        if (!empty($v['civil_status'])) {
            $civilStatusEnum = $v['civil_status'] instanceof CivilStatus
                ? $v['civil_status']
                : CivilStatus::tryFrom((string) $v['civil_status']);
        }

        return new self(
            firstName:             $v['first_name'],
            lastName:              $v['last_name'],
            dateOfBirth:           $v['date_of_birth'],
            email:                 $v['email'],
            password:              $v['password'],
            middleName:            $v['middle_name'] ?? null,
            suffix:                $v['suffix'] ?? null,
            phone:                 $v['phone'] ?? null,
            mobile:                $v['mobile'] ?? null,
            gender:                $v['gender'] ?? null,
            civilStatus:           $civilStatusEnum,
            currentAddress:        $v['current_address'] ?? null,
            city:                  $v['city'] ?? null,
            province:              $v['province'] ?? null,
            appliedPosition:       $v['applied_position'] ?? null,
            skillCategory:         $v['skill_category'] ?? null,
            tradeOrOccupation:     $v['trade_or_occupation'] ?? null,
            jlptLevel:             $v['jlpt_level'] ?? null,
            willingToBeDeployed:   $v['willing_to_be_deployed'] ?? true,
            previousJapanExperience: $v['previous_japan_experience'] ?? false,
            yearsJapanExperience:  $v['years_japan_experience'] ?? 0,
            emergencyContactName:         $v['emergency_contact_name'] ?? null,
            emergencyContactRelationship: $v['emergency_contact_relationship'] ?? null,
            emergencyContactPhone:        $v['emergency_contact_phone'] ?? null,
        );
    }
}