<?php

namespace App\Http\Requests\v1\DocumentVersion;

use Illuminate\Foundation\Http\FormRequest;

class GetDocumentVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('document_version'));
    }

    public function rules(): array
    {
        return [];
    }
}