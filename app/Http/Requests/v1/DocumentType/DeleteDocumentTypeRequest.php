<?php

namespace App\Http\Requests\v1\DocumentType;

use Illuminate\Foundation\Http\FormRequest;

class DeleteDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('document_type'));
    }

    public function rules(): array
    {
        return [];
    }
}