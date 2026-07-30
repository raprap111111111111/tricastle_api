<?php

// app/Domain/ApplicantLifestyle/Mappers/ApplicantLifestyleMapper.php

namespace App\Domain\ApplicantLifestyle\Mappers;

use App\Domain\ApplicantLifestyle\DTOs\UpsertApplicantLifestyleDTO;
use App\Http\Requests\v1\ApplicantLifestyle\UpsertApplicantLifestyleRequest;

class ApplicantLifestyleMapper
{
    public static function fromUpsertRequest(UpsertApplicantLifestyleRequest $request): UpsertApplicantLifestyleDTO
    {
        return new UpsertApplicantLifestyleDTO(
            applicantId:         (int) $request->validated('applicant_id'),

            isSmoking:           $request->has('is_smoking')
                                    ? (bool) $request->validated('is_smoking')
                                    : null,
            isDrinkingAlcohol:   $request->has('is_drinking_alcohol')
                                    ? (bool) $request->validated('is_drinking_alcohol')
                                    : null,
            isUsingDrugs:        $request->has('is_using_drugs')
                                    ? (bool) $request->validated('is_using_drugs')
                                    : null,

            wasSmoking:          $request->has('was_smoking')
                                    ? (bool) $request->validated('was_smoking')
                                    : null,
            wasDrinkingAlcohol:  $request->has('was_drinking_alcohol')
                                    ? (bool) $request->validated('was_drinking_alcohol')
                                    : null,
            wasUsingDrugs:       $request->has('was_using_drugs')
                                    ? (bool) $request->validated('was_using_drugs')
                                    : null,

            smokingFrequency:    $request->validated('smoking_frequency'),
            drinkingFrequency:   $request->validated('drinking_frequency'),
            drugsNotes:          $request->validated('drugs_notes'),

            hasMedicalCondition: $request->has('has_medical_condition')
                                    ? (bool) $request->validated('has_medical_condition')
                                    : null,
            medicalNotes:        $request->validated('medical_notes'),
            hasAllergies:        $request->has('has_allergies')
                                    ? (bool) $request->validated('has_allergies')
                                    : null,
            allergiesNotes:      $request->validated('allergies_notes'),
        );
    }
}