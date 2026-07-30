<?php

namespace App\Http\Requests\v1\ApplicantDocument;

use Illuminate\Foundation\Http\FormRequest;

class VerifyApplicantDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('verify', $this->route('applicant_document'));
    }

    public function rules(): array
    {
        return [
            'notes'          => ['nullable', 'string', 'max:1000'],
            'validated_data' => ['nullable', 'array'],
        ];
    }
}