<?php

namespace App\Http\Requests\v1\DocumentVerification;

use Illuminate\Foundation\Http\FormRequest;

class StartDocumentVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('start', $this->route('document_verification'));
    }

    public function rules(): array
    {
        return [];
    }
}