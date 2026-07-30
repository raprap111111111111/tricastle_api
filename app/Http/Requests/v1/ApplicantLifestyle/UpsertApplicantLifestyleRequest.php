<?php

namespace App\Http\Requests\v1\ApplicantLifestyle;

use App\Models\ApplicantLifestyle;
use Illuminate\Foundation\Http\FormRequest;

class UpsertApplicantLifestyleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Anyone who can create OR update lifestyle can call upsert
        return $this->user()->can('create', ApplicantLifestyle::class)
            || $this->user()->can('update', ApplicantLifestyle::class);
    }

    public function rules(): array
    {
        return [
            'applicant_id'          => ['required', 'integer', 'exists:applicants,id'],

            // Current habits
            'is_smoking'            => ['sometimes', 'boolean'],
            'is_drinking_alcohol'   => ['sometimes', 'boolean'],
            'is_using_drugs'        => ['sometimes', 'boolean'],

            // Past habits
            'was_smoking'           => ['sometimes', 'boolean'],
            'was_drinking_alcohol'  => ['sometimes', 'boolean'],
            'was_using_drugs'       => ['sometimes', 'boolean'],

            // Frequencies / notes
            'smoking_frequency'     => ['sometimes', 'nullable', 'string', 'max:255'],
            'drinking_frequency'    => ['sometimes', 'nullable', 'string', 'max:255'],
            'drugs_notes'           => ['sometimes', 'nullable', 'string', 'max:5000'],

            // Health
            'has_medical_condition' => ['sometimes', 'boolean'],
            'medical_notes'         => ['sometimes', 'nullable', 'string', 'max:5000'],
            'has_allergies'         => ['sometimes', 'boolean'],
            'allergies_notes'       => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_id.exists' => 'The selected applicant does not exist.',
        ];
    }
}