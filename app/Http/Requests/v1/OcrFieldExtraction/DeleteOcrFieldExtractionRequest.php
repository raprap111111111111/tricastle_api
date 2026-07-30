<?php

namespace App\Http\Requests\v1\OcrFieldExtraction;

use Illuminate\Foundation\Http\FormRequest;

class DeleteOcrFieldExtractionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('ocr_field_extraction'));
    }

    public function rules(): array
    {
        return [];
    }
}