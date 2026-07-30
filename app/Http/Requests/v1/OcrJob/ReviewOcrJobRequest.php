<?php

namespace App\Http\Requests\v1\OcrJob;

use Illuminate\Foundation\Http\FormRequest;

class ReviewOcrJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('review', $this->route('ocr_job'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:completed,requires_review,failed'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ];
    }
}