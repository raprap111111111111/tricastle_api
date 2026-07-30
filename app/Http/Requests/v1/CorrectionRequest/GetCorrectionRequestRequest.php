<?php

namespace App\Http\Requests\v1\CorrectionRequest;

use Illuminate\Foundation\Http\FormRequest;

class GetCorrectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('correction_request'));
    }

    public function rules(): array
    {
        return [];
    }
}