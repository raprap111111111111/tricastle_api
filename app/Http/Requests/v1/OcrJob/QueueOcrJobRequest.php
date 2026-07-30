<?php

namespace App\Http\Requests\v1\OcrJob;

use Illuminate\Foundation\Http\FormRequest;

class QueueOcrJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('queue', $this->route('ocr_job'));
    }

    public function rules(): array
    {
        return [
            'priority' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }
}