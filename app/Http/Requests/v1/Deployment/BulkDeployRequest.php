<?php

namespace App\Http\Requests\v1\Deployment;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;

class BulkDeployRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Applicant::class);
    }

    public function rules(): array
    {
        return [
            'applicant_batch_ids'   => ['required', 'array', 'min:1', 'max:100'],
            'applicant_batch_ids.*' => ['integer', 'exists:applicant_batches,id'],

            // Same deployment data applied to all
            'deployment_country'       => ['required', 'string', 'max:100'],
            'deployment_company'       => ['required', 'string', 'max:200'],
            'deployment_date'          => ['required', 'date'],
            'deployment_position'      => ['nullable', 'string', 'max:150'],
            'contract_duration_months' => ['nullable', 'integer', 'min:1', 'max:120'],
            'contract_start_date'      => ['nullable', 'date'],
            'contract_end_date'        => ['nullable', 'date', 'after_or_equal:contract_start_date'],
            'monthly_salary'           => ['nullable', 'numeric', 'min:0'],
            'salary_currency'          => ['nullable', 'string', 'size:3'],
            'flight_date'              => ['nullable', 'date'],
            'visa_type'                => ['nullable', 'string', 'max:100'],
            'deployment_notes'         => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_batch_ids.required' => 'Please select at least one applicant to deploy.',
            'applicant_batch_ids.min'      => 'Please select at least one applicant to deploy.',
            'applicant_batch_ids.max'      => 'You can only deploy up to 100 applicants at once.',
        ];
    }
}