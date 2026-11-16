<?php

namespace App\Http\Requests\v1\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class BulkFinalListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('applicant.update'); // or your preferred permission
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:applicants,id'],
        ];
    }
}