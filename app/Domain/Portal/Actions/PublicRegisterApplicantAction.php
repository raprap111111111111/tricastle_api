<?php

namespace App\Domain\Portal\Actions;

use App\Domain\Portal\DTOs\PublicRegisterApplicantDTO;
use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use App\Models\ApplicantPortalAccount;
use Illuminate\Support\Facades\DB;

class PublicRegisterApplicantAction
{
    public function execute(PublicRegisterApplicantDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            // 1. Create Applicant
            $applicant = Applicant::create([
                'status'                         => ApplicantStatus::SelfRegistered,
                'created_by'                     => null,
                'first_name'                     => $dto->firstName,
                'middle_name'                    => $dto->middleName,
                'last_name'                      => $dto->lastName,
                'suffix'                         => $dto->suffix,
                'date_of_birth'                  => $dto->dateOfBirth,
                'email'                          => $dto->email,
                'phone'                          => $dto->phone,
                'mobile'                         => $dto->mobile,
                'gender'                         => $dto->gender,
                'civil_status'                   => $dto->civilStatus,
                'current_address'                => $dto->currentAddress,
                'city'                           => $dto->city,
                'province'                       => $dto->province,
                'applied_position'               => $dto->appliedPosition,
                'skill_category'                 => $dto->skillCategory,
                'trade_or_occupation'            => $dto->tradeOrOccupation,
                'jlpt_level'                     => $dto->jlptLevel,
                'willing_to_be_deployed'         => $dto->willingToBeDeployed ?? true,
                'previous_japan_experience'      => $dto->previousJapanExperience ?? false,
                'years_japan_experience'         => $dto->yearsJapanExperience ?? 0,
                'emergency_contact_name'         => $dto->emergencyContactName,
                'emergency_contact_relationship' => $dto->emergencyContactRelationship,
                'emergency_contact_phone'        => $dto->emergencyContactPhone,
            ]);

            // 2. Create Portal Account (status: disabled prevents login until staff accepts)
            $account = ApplicantPortalAccount::create([
                'applicant_id' => $applicant->id,
                'email'        => $dto->email,
                'password'     => $dto->password,
                'status'       => 'disabled',
            ]);

            return [
                'applicant' => $applicant,
                'account'   => $account,
            ];
        });
    }
}