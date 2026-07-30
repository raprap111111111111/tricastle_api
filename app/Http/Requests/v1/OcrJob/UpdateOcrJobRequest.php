<?php

namespace App\Http\Requests\v1\OcrJob;

use App\Models\OcrJob;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOcrJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('ocr_job'));
    }

    public function rules(): array
    {
        return [
            'status_message'  => ['nullable', 'string', 'max:1000'],
            'provider'        => ['nullable', 'in:aws_textract,google_vision,azure_form_recognizer,tesseract,openai_vision,manual,custom_api'],
            'provider_config' => ['nullable', 'array'],
            'priority'        => ['nullable', 'integer', 'min:1', 'max:10'],
            'max_attempts'    => ['nullable', 'integer', 'min:1', 'max:10'],
            'notes'           => ['nullable', 'string', 'max:1000'],
            'metadata'        => ['nullable', 'array'],
        ];
    }
}