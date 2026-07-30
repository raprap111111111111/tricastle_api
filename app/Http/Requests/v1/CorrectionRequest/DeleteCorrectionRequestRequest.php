<?php

namespace App\Http\Requests\v1\CorrectionRequest;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCorrectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('correction_request'));
    }

    public function rules(): array
    {
        return [];
    }
}