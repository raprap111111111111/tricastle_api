<?php

namespace App\Http\Requests\v1\CorrectionRequest;

use Illuminate\Foundation\Http\FormRequest;

class CompleteCorrectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('complete', $this->route('correction_request'));
    }

    public function rules(): array
    {
        return [
            'correction_data' => ['nullable', 'array'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ];
    }
}