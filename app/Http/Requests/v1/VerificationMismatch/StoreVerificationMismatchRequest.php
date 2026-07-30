<?php

namespace App\Http\Requests\v1\VerificationMismatch;

use App\Models\VerificationMismatch;
use Illuminate\Foundation\Http\FormRequest;

class StoreVerificationMismatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', VerificationMismatch::class);
    }

    public function rules(): array
    {
        return [
            'document_verification_id' => ['required', 'integer', 'exists:document_verifications,id'],
            'field_name'               => ['required', 'string', 'max:255'],
            'field_label'              => ['required', 'string', 'max:255'],
            'source_value'             => ['nullable', 'string'],
            'entered_value'            => ['nullable', 'string'],
            'severity'                 => ['nullable', 'in:low,moderate,critical'],
            'mismatch_type'            => ['nullable', 'in:value_mismatch,missing_in_document,missing_in_system,format_mismatch,date_mismatch'],
            'status'                   => ['nullable', 'in:open,correction_requested,corrected,waived,escalated'],
        ];
    }
}