<?php

namespace App\Http\Requests\v1\ApplicantDocument;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('applicant_document'));
    }

    public function rules(): array
    {
        return [
            'document_date'  => ['nullable', 'date'],
            'expiry_date'    => ['nullable', 'date'],
            'priority'       => ['sometimes', 'in:low,normal,high,urgent'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'metadata'       => ['nullable', 'array'],
            'validated_data' => ['nullable', 'array'],
        ];
    }
}