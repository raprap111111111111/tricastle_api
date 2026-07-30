<?php

namespace App\Http\Requests\v1\ApplicantEducation;

use Illuminate\Foundation\Http\FormRequest;

class DeleteApplicantEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'delete',
            $this->route('applicant_education')
        );
    }

    public function rules(): array
    {
        return [];
    }
}