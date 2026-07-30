<?php

namespace App\Http\Requests\v1\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class DeleteApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('applicant'));
    }

    public function rules(): array
    {
        return [];
    }
}