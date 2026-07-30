<?php

namespace App\Http\Requests\v1\DocumentType;

use App\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DocumentType::class);
    }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'code'               => ['required', 'string', 'max:50', 'unique:document_types,code'],
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