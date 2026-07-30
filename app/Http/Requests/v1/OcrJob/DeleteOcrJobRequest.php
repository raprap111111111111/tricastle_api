<?php

namespace App\Http\Requests\v1\OcrJob;

use Illuminate\Foundation\Http\FormRequest;

class DeleteOcrJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('ocr_job'));
    }

    public function rules(): array
    {
        return [];
    }
}