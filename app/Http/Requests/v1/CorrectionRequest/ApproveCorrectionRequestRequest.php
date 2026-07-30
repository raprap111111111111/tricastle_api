<?php

namespace App\Http\Requests\v1\CorrectionRequest;

use Illuminate\Foundation\Http\FormRequest;

class ApproveCorrectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('approve', $this->route('correction_request'));
    }

    public function rules(): array
    {
        return [
            'notes'    => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date', 'after:today'],
        ];
    }
}