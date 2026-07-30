<?php

namespace App\Http\Requests\v1\DocumentType;

use App\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('document_type'));
    }

    public function rules(): array
    {
        $documentType   = $this->route('document_type');
        $documentTypeId = $documentType instanceof DocumentType
            ? $documentType->id
            : $documentType;

        return [
            'name'               => ['sometimes', 'string', 'max:255'],
            'code'               => [
                'sometimes', 'string', 'max:50',
                Rule::unique('document_types', 'code')->ignore($documentTypeId),
            ],
            'description'        => ['nullable', 'string'],
            'required_fields'    => ['nullable', 'array'],
            'required_fields.*'  => ['string'],
            'validation_rules'   => ['nullable', 'array'],
            'is_required'        => ['nullable', 'boolean'],
            'is_active'          => ['nullable', 'boolean'],
            'validity_days'      => ['nullable', 'integer', 'min:1'],
            'expiry_warning_days'=> ['nullable', 'integer', 'min:1', 'max:365'],
            'category'           => ['nullable', 'in:primary,supporting'],
            'sort_order'         => ['nullable', 'integer', 'min:0'],
        ];
    }
}