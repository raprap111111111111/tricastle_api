<?php

namespace App\Http\Requests\v1\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class ExportInsuranceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'departure_date'                        => ['required', 'string'],
            'applicants'                            => ['required_without:applicant_ids', 'array', 'min:1'],
            'applicants.*.id'                      => ['required_with:applicants', 'integer', 'exists:applicants,id'],
            'applicants.*.date_of_birth'            => ['nullable', 'string'],
            'applicants.*.passport_number'          => ['nullable', 'string'],
            'applicants.*.occupation'               => ['nullable', 'string'],
            'applicants.*.foreign_employer'         => ['nullable', 'string'],
            'applicants.*.country_destination'      => ['nullable', 'string'],
            'applicants.*.contract_duration_months' => ['nullable'],
            
            // Backward compatibility
            'applicant_ids'                         => ['required_without:applicants', 'array', 'min:1'],
            'applicant_ids.*'                       => ['integer', 'exists:applicants,id'],
        ];
    }
}