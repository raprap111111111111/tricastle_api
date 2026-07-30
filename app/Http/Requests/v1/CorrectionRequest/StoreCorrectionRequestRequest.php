<?php

namespace App\Http\Requests\v1\CorrectionRequest;

use App\Models\CorrectionRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', CorrectionRequest::class);
    }

    public function rules(): array
    {
        return [
            'document_verification_id' => ['required', 'integer', 'exists:document_verifications,id'],
            'applicant_document_id'    => ['required', 'integer', 'exists:applicant_documents,id'],
            'description'              => ['required', 'string', 'max:2000'],
            'severity'                 => ['nullable', 'in:low,moderate,critical'],
            'fields_to_correct'        => ['nullable', 'array'],
            'fields_to_correct.*'      => ['string'],
            'correction_data'          => ['nullable', 'array'],
            'justification'            => ['nullable', 'string', 'max:2000'],
            'requires_approval'        => ['nullable', 'boolean'],
            'requires_new_document'    => ['nullable', 'boolean'],
            'due_date'                 => ['nullable', 'date', 'after:today'],
        ];
    }
}