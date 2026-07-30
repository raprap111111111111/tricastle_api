<?php

namespace App\Http\Requests\v1\ApplicantEmployment;

use Illuminate\Foundation\Http\FormRequest;

class GetApplicantEmploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'view',
            $this->route('applicant_employment')
        );
    }

    public function rules(): array
    {
        return [];
    }
}