<?php

namespace App\Http\Requests\v1\DocumentVersion;

use Illuminate\Foundation\Http\FormRequest;

class DeleteDocumentVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('document_version'));
    }

    public function rules(): array
    {
        return [];
    }
}