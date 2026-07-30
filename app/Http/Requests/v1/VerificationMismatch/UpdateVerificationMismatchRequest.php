<?php

namespace App\Http\Requests\v1\VerificationMismatch;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVerificationMismatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('verification_mismatch'));
    }

    public function rules(): array
    {
        return [
            'field_name'       => ['sometimes', 'string', 'max:255'],
            'field_label'      => ['sometimes', 'string', 'max:255'],
            'source_value'     => ['nullable', 'string'],
            'entered_value'    => ['nullable', 'string'],
            'severity'         => ['sometimes', 'in:low,moderate,critical'],
            'mismatch_type'    => ['sometimes', 'in:value_mismatch,missing_in_document,missing_in_system,format_mismatch,date_mismatch'],
            'status'           => ['sometimes', 'in:open,correction_requested,corrected,waived,escalated'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}