<?php

namespace App\Http\Requests\v1\ApplicantTattoo;

use Illuminate\Foundation\Http\FormRequest;

class ToggleApplicantTattooVisibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'update',
            $this->route('applicant_tattoo')
        );
    }

    public function rules(): array
    {
        return [];
    }
}