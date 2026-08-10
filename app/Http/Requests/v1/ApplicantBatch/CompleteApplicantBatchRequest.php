<?php

namespace App\Http\Requests\v1\ApplicantBatch;

use Illuminate\Foundation\Http\FormRequest;

class CompleteApplicantBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'completion_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}