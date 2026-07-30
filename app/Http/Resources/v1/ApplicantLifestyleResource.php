<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantLifestyleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'applicant_id'          => $this->applicant_id,

            'applicant'             => $this->whenLoaded('applicant', function () {
                return [
                    'id'   => $this->applicant->id,
                    'name' => $this->applicant->name ?? null,
                ];
            }),

            // Current habits
            'is_smoking'            => (bool) $this->is_smoking,
            'is_drinking_alcohol'   => (bool) $this->is_drinking_alcohol,
            'is_using_drugs'        => (bool) $this->is_using_drugs,

            // Past habits
            'was_smoking'           => (bool) $this->was_smoking,
            'was_drinking_alcohol'  => (bool) $this->was_drinking_alcohol,
            'was_using_drugs'       => (bool) $this->was_using_drugs,

            // Frequencies / notes
            'smoking_frequency'     => $this->smoking_frequency,
            'drinking_frequency'    => $this->drinking_frequency,
            'drugs_notes'           => $this->drugs_notes,

            // Health
            'has_medical_condition' => (bool) $this->has_medical_condition,
            'medical_notes'         => $this->medical_notes,
            'has_allergies'         => (bool) $this->has_allergies,
            'allergies_notes'       => $this->allergies_notes,

            'created_at'            => $this->created_at?->toIso8601String(),
            'updated_at'            => $this->updated_at?->toIso8601String(),
        ];
    }
}