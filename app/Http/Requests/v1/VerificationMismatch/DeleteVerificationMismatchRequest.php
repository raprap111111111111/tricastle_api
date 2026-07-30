<?php

namespace App\Http\Requests\v1\VerificationMismatch;

use Illuminate\Foundation\Http\FormRequest;

class DeleteVerificationMismatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('verification_mismatch'));
    }

    public function rules(): array
    {
        return [];
    }
}