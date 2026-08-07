<?php

namespace App\Http\Requests\v1\Deployment;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;

class CancelDeploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Applicant::class);
    }

    public function rules(): array
    {
        return [
            'cancellation_reason' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'cancellation_reason.required' => 'Please provide a reason for cancellation.',
            'cancellation_reason.min'      => 'Cancellation reason must be at least 10 characters.',
        ];
    }
}