<?php

namespace App\Http\Requests\v1\ApplicantEmployment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantEmploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'update',
            $this->route('applicant_employment')
        );
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_current') && $this->boolean('is_current')) {
            $this->merge(['date_ended' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'company_name'       => ['sometimes', 'required', 'string', 'max:255'],
            'position'           => ['sometimes', 'required', 'string', 'max:255'],
            'industry'           => ['sometimes', 'nullable', 'string', 'max:255'],
            'job_description'    => ['sometimes', 'nullable', 'string', 'max:5000'],

            'date_started'       => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'date_ended'         => [
                'sometimes', 'nullable', 'date',
                'after_or_equal:date_started',
                'before_or_equal:today',
            ],
            'is_current'         => ['sometimes', 'boolean'],

            'country'            => ['sometimes', 'nullable', 'string', 'max:100'],
            'city'               => ['sometimes', 'nullable', 'string', 'max:100'],

            'salary'             => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'salary_currency'    => ['sometimes', 'nullable', 'string', 'size:3'],

            'reason_for_leaving' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}