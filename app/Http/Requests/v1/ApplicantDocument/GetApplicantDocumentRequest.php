<?php

namespace App\Http\Requests\v1\ApplicantDocument;

use Illuminate\Foundation\Http\FormRequest;

class GetApplicantDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('applicant_document'));
    }

    public function rules(): array
    {
        return [];
    }
}