<?php

namespace App\Http\Requests\v1\ApplicantLifestyle;

use Illuminate\Foundation\Http\FormRequest;

class GetApplicantLifestyleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'view',
            $this->route('applicant_lifestyle')
        );
    }

    public function rules(): array
    {
        return [];
    }
}