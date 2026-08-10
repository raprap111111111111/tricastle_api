<?php

namespace App\Http\Requests\v1\Deployment;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;

class DeployApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Applicant::class);
    }

    public function rules(): array
    {
        return [
            // ─── Required fields ────────────────────────
            'deployment_country' => ['required', 'string', 'max:100'],
            'deployment_company' => ['required', 'string', 'max:200'],
            'deployment_date'    => ['required', 'date'],

            // ─── Optional fields ────────────────────────
            'deployment_position'      => ['nullable', 'string', 'max:150'],
            'contract_duration_months' => ['nullable', 'integer', 'min:1', 'max:120'],
            'contract_start_date'      => ['nullable', 'date'],
            'contract_end_date'        => ['nullable', 'date', 'after_or_equal:contract_start_date'],
            'monthly_salary'           => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'salary_currency'          => ['nullable', 'string', 'size:3'],
            'flight_date'              => ['nullable', 'date'],
            'visa_type'                => ['nullable', 'string', 'max:100'],
            'deployment_notes'         => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'deployment_country.required' => 'Please specify the deployment country.',
            'deployment_company.required' => 'Please specify the deployment company.',
            'deployment_date.required'    => 'Please specify the deployment date.',
            'contract_end_date.after_or_equal' => 'Contract end date must be after the start date.',
        ];
    }
}