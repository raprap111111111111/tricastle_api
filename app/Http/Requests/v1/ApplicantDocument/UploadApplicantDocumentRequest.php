<?php

namespace App\Http\Requests\v1\ApplicantDocument;

use App\Models\ApplicantDocument;
use Illuminate\Foundation\Http\FormRequest;

class UploadApplicantDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ApplicantDocument::class);
    }

    public function rules(): array
    {
        return [
            'applicant_id'    => ['required', 'integer', 'exists:applicants,id'],
            'document_type_id'=> ['required', 'integer', 'exists:document_types,id'],
            'file'            => ['required', 'file', 'max:51200', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'document_date'   => ['nullable', 'date'],
            'expiry_date'     => ['nullable', 'date', 'after:today'],
            'priority'        => ['nullable', 'in:low,normal,high,urgent'],
            'notes'           => ['nullable', 'string', 'max:1000'],
            'metadata'        => ['nullable', 'array'],
        ];
    }
}