<?php

namespace App\Http\Requests\v1\DocumentVerification;

use Illuminate\Foundation\Http\FormRequest;

class RejectDocumentVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reject', $this->route('document_verification'));
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'max:1000'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }
}