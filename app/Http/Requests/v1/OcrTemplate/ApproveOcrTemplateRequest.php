<?php

// app/Http/Requests/v1/OcrTemplate/ApproveOcrTemplateRequest.php

namespace App\Http\Requests\v1\OcrTemplate;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOcrTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('approve', $this->route('ocr_template'));
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}