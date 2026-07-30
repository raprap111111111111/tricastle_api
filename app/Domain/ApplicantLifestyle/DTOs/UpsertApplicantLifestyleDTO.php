<?php

// app/Domain/ApplicantLifestyle/DTOs/UpsertApplicantLifestyleDTO.php

namespace App\Domain\ApplicantLifestyle\DTOs;

final readonly class UpsertApplicantLifestyleDTO
{
    public function __construct(
        public int     $applicantId,

        // Current habits
        public ?bool   $isSmoking = null,
        public ?bool   $isDrinkingAlcohol = null,
        public ?bool   $isUsingDrugs = null,

        // Past habits
        public ?bool   $wasSmoking = null,
        public ?bool   $wasDrinkingAlcohol = null,
        public ?bool   $wasUsingDrugs = null,

        // Frequencies / notes
        public ?string $smokingFrequency = null,
        public ?string $drinkingFrequency = null,
        public ?string $drugsNotes = null,

        // Health
        public ?bool   $hasMedicalCondition = null,
        public ?string $medicalNotes = null,
        public ?bool   $hasAllergies = null,
        public ?string $allergiesNotes = null,
    ) {}
}