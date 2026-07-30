<?php

// app/Http/Requests/v1/OcrTemplate/GetOcrTemplateRequest.php

namespace App\Http\Requests\v1\OcrTemplate;

use Illuminate\Foundation\Http\FormRequest;

class GetOcrTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('ocr_template'));
    }

    public function rules(): array
    {
        return [];
    }
}