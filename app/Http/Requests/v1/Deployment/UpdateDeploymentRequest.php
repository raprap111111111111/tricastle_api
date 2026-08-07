<?php

namespace App\Http\Requests\v1\Deployment;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Applicant::class);
    }

    public function rules(): array
    {
        return [
            // All optional (only send what's being changed)
            'deployment_country'       => ['nullable', 'string', 'max:100'],
            'deployment_company'       => ['nullable', 'string', 'max:200'],
            'deployment_position'      => ['nullable', 'string', 'max:150'],
            'deployment_date'          => ['nullable', 'date'],
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
}