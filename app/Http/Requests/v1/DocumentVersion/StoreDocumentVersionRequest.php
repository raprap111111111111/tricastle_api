<?php

namespace App\Http\Requests\v1\DocumentVersion;

use App\Models\DocumentVersion;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DocumentVersion::class);
    }

    public function rules(): array
    {
        return [
            'applicant_document_id' => ['required', 'integer', 'exists:applicant_documents,id'],
            'file'                  => ['required', 'file', 'max:51200', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'change_reason'         => ['nullable', 'string', 'max:500'],
            'extracted_data'        => ['nullable', 'array'],
        ];
    }
}