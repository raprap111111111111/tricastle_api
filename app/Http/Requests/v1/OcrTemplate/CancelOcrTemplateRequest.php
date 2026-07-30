<?php

// app/Http/Requests/v1/OcrTemplate/CancelOcrTemplateRequest.php

namespace App\Http\Requests\v1\OcrTemplate;

use Illuminate\Foundation\Http\FormRequest;

class CancelOcrTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cancel', $this->route('ocr_template'));
    }

    public function rules(): array
    {
        return [];
    }
}