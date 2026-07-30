<?php

namespace App\Http\Requests\v1\Company;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Company::class);
    }

    public function rules(): array
    {
        return [
            'code'           => ['required', 'string', 'max:100', 'unique:companies,code'],
            'name'           => ['required', 'string', 'max:255'],
            'name_japanese'  => ['nullable', 'string', 'max:255'],
            'category_id'    => ['required', 'integer', 'exists:company_categories,id'],

            'address'        => ['nullable', 'string', 'max:1000'],
            'city'           => ['nullable', 'string', 'max:100'],
            'prefecture'     => ['nullable', 'string', 'max:100'],
            'postal_code'    => ['nullable', 'string', 'max:20'],
            'country'        => ['nullable', 'string', 'max:100'],

            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email'  => ['nullable', 'email', 'max:255'],
            'contact_phone'  => ['nullable', 'string', 'max:50'],

            'description'    => ['nullable', 'string', 'max:5000'],
            'is_active'      => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique'          => 'A company with this code already exists.',
            'category_id.exists'   => 'The selected category does not exist.',
        ];
    }
}