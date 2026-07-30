<?php

namespace App\Http\Requests\v1\CorrectionRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCorrectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('correction_request'));
    }

    public function rules(): array
    {
        return [
            'description'          => ['sometimes', 'string', 'max:2000'],
            'severity'             => ['sometimes', 'in:low,moderate,critical'],
            'fields_to_correct'    => ['nullable', 'array'],
            'fields_to_correct.*'  => ['string'],
            'correction_data'      => ['nullable', 'array'],
            'justification'        => ['nullable', 'string', 'max:2000'],
            'requires_approval'    => ['nullable', 'boolean'],
            'requires_new_document'=> ['nullable', 'boolean'],
            'due_date'             => ['nullable', 'date'],
        ];
    }
}