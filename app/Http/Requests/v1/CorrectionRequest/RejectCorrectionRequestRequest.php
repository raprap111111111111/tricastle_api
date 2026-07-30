<?php

namespace App\Http\Requests\v1\CorrectionRequest;

use Illuminate\Foundation\Http\FormRequest;

class RejectCorrectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reject', $this->route('correction_request'));
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:1000'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ];
    }
}