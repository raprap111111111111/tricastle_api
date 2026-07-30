<?php

// app/Http/Requests/v1/OcrTemplate/CompleteOcrTemplateRequest.php

namespace App\Http\Requests\v1\OcrTemplate;

use Illuminate\Foundation\Http\FormRequest;

class CompleteOcrTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('complete', $this->route('ocr_template'));
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}