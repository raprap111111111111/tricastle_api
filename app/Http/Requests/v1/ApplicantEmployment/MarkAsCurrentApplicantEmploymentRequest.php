<?php

namespace App\Http\Requests\v1\ApplicantEmployment;

use Illuminate\Foundation\Http\FormRequest;

class MarkAsCurrentApplicantEmploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'update',
            $this->route('applicant_employment')
        );
    }

    public function rules(): array
    {
        return [];
    }
}