<?php

namespace App\Http\Requests\v1\OcrJob;

use Illuminate\Foundation\Http\FormRequest;

class CancelOcrJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cancel', $this->route('ocr_job'));
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}