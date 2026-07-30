<?php

// app/Http/Requests/v1/OcrTemplate/RejectOcrTemplateRequest.php

namespace App\Http\Requests\v1\OcrTemplate;

use Illuminate\Foundation\Http\FormRequest;

class RejectOcrTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reject', $this->route('ocr_template'));
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:1000'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ];
    }
}