<?php

namespace App\Http\Requests\v1\DocumentVerification;

use App\Models\DocumentVerification;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DocumentVerification::class);
    }

    public function rules(): array
    {
        return [
            'applicant_document_id' => ['required', 'integer', 'exists:applicant_documents,id'],
            'verification_data'     => ['nullable', 'array'],
            'source_data'           => ['nullable', 'array'],
            'notes'                 => ['nullable', 'string', 'max:1000'],
        ];
    }
}