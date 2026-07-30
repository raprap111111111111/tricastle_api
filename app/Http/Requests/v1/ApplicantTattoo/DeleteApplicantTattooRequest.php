<?php

namespace App\Http\Requests\v1\ApplicantTattoo;

use Illuminate\Foundation\Http\FormRequest;

class DeleteApplicantTattooRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'delete',
            $this->route('applicant_tattoo')
        );
    }

    public function rules(): array
    {
        return [];
    }
}