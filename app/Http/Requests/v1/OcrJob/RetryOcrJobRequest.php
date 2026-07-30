<?php

namespace App\Http\Requests\v1\OcrJob;

use Illuminate\Foundation\Http\FormRequest;

class RetryOcrJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('retry', $this->route('ocr_job'));
    }

    public function rules(): array
    {
        return [
            'provider' => ['nullable', 'in:aws_textract,google_vision,azure_form_recognizer,tesseract,openai_vision,manual,custom_api'],
            'priority' => ['nullable', 'integer', 'min:1', 'max:10'],
            'notes'    => ['nullable', 'string', 'max:1000'],
        ];
    }
}