<?php

namespace App\Http\Requests\v1\ApplicantEmployment;

use App\Models\ApplicantEmployment;
use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantEmploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ApplicantEmployment::class);
    }

    protected function prepareForValidation(): void
    {
        // If is_current is true, force date_ended to null
        if ($this->boolean('is_current')) {
            $this->merge(['date_ended' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'applicant_id'       => ['required', 'integer', 'exists:applicants,id'],
            'company_name'       => ['required', 'string', 'max:255'],
            'position'           => ['required', 'string', 'max:255'],
            'industry'           => ['nullable', 'string', 'max:255'],
            'job_description'    => ['nullable', 'string', 'max:5000'],

            'date_started'       => ['required', 'date', 'before_or_equal:today'],
            'date_ended'         => [
                'nullable',
                'required_if:is_current,false',
                'date',
                'after_or_equal:date_started',
                'before_or_equal:today',
            ],
            'is_current'         => ['nullable', 'boolean'],

            'country'            => ['nullable', 'string', 'max:100'],
            'city'               => ['nullable', 'string', 'max:100'],

            'salary'             => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'salary_currency'    => ['nullable', 'string', 'size:3'],

            'reason_for_leaving' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_id.exists'    => 'The selected applicant does not exist.',
            'date_ended.required_if' => 'Date ended is required unless this is your current job.',
            'date_ended.after_or_equal' => 'Date ended must be on or after date started.',
        ];
    }
}