<?php

namespace App\Domain\ApplicantLifestyle\Actions;

use App\Domain\ApplicantLifestyle\DTOs\UpsertApplicantLifestyleDTO;
use App\Domain\ApplicantLifestyle\Repositories\ApplicantLifestyleRepository;
use App\Models\ApplicantLifestyle;

class UpsertApplicantLifestyleAction
{
    public function __construct(
        private readonly ApplicantLifestyleRepository $repository
    ) {}

    public function execute(UpsertApplicantLifestyleDTO $dto): ApplicantLifestyle
    {
        $payload = array_filter([
            // Current habits
            'is_smoking'            => $dto->isSmoking,
            'is_drinking_alcohol'   => $dto->isDrinkingAlcohol,
            'is_using_drugs'        => $dto->isUsingDrugs,

            // Past habits
            'was_smoking'           => $dto->wasSmoking,
            'was_drinking_alcohol'  => $dto->wasDrinkingAlcohol,
            'was_using_drugs'       => $dto->wasUsingDrugs,

            // Frequencies / notes
            'smoking_frequency'     => $dto->smokingFrequency,
            'drinking_frequency'    => $dto->drinkingFrequency,
            'drugs_notes'           => $dto->drugsNotes,

            // Health
            'has_medical_condition' => $dto->hasMedicalCondition,
            'medical_notes'         => $dto->medicalNotes,
            'has_allergies'         => $dto->hasAllergies,
            'allergies_notes'       => $dto->allergiesNotes,
        ], fn ($value) => $value !== null);

        return $this->repository->upsertForApplicant($dto->applicantId, $payload);
    }
}