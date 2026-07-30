<?php

namespace App\Http\Requests\v1\ApplicantDocument;

use Illuminate\Foundation\Http\FormRequest;

class DeleteApplicantDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('applicant_document'));
    }

    public function rules(): array
    {
        return [];
    }
}