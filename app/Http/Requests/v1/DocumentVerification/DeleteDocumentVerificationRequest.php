<?php

namespace App\Http\Requests\v1\DocumentVerification;

use Illuminate\Foundation\Http\FormRequest;

class DeleteDocumentVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('document_verification'));
    }

    public function rules(): array
    {
        return [];
    }
}