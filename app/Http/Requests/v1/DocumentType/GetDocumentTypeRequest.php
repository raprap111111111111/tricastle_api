<?php

namespace App\Http\Requests\v1\DocumentType;

use Illuminate\Foundation\Http\FormRequest;

class GetDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('document_type'));
    }

    public function rules(): array
    {
        return [];
    }
}