<?php

namespace App\Http\Requests\v1\OcrFieldExtraction;

use Illuminate\Foundation\Http\FormRequest;

class RejectOcrFieldExtractionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reject', $this->route('ocr_field_extraction'));
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}