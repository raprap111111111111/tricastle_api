<?php

namespace App\Http\Requests\v1\OcrFieldExtraction;

use Illuminate\Foundation\Http\FormRequest;

class CorrectOcrFieldExtractionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('correct', $this->route('ocr_field_extraction'));
    }

    public function rules(): array
    {
        return [
            'corrected_value'  => ['required', 'string', 'max:1000'],
            'correction_reason' => ['nullable', 'string', 'max:1000'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }
}