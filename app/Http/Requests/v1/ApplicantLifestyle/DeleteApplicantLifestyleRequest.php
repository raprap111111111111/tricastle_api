<?php

namespace App\Http\Requests\v1\ApplicantLifestyle;

use Illuminate\Foundation\Http\FormRequest;

class DeleteApplicantLifestyleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'delete',
            $this->route('applicant_lifestyle')
        );
    }

    public function rules(): array
    {
        return [];
    }
}