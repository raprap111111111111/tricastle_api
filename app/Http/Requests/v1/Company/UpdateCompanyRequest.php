<?php

namespace App\Http\Requests\v1\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Simple and reliable – matches what the policy actually checks
        return $this->user()->can('company.update');
    }

    public function rules(): array
    {
        $companyId = $this->route('company')?->id
            ?? $this->route('company');   // fallback if still an ID

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('companies', 'code')->ignore($companyId),
            ],
            'name'           => ['sometimes', 'required', 'string', 'max:255'],
            'name_japanese'  => ['sometimes', 'nullable', 'string', 'max:255'],
            'category_id'    => ['sometimes', 'required', 'integer', 'exists:company_categories,id'],

            'address'        => ['sometimes', 'nullable', 'string', 'max:1000'],
            'city'           => ['sometimes', 'nullable', 'string', 'max:100'],
            'prefecture'     => ['sometimes', 'nullable', 'string', 'max:100'],
            'postal_code'    => ['sometimes', 'nullable', 'string', 'max:20'],
            'country'        => ['sometimes', 'nullable', 'string', 'max:100'],

            'contact_person' => ['sometimes', 'nullable', 'string', 'max:255'],
            'contact_email'  => ['sometimes', 'nullable', 'email', 'max:255'],
            'contact_phone'  => ['sometimes', 'nullable', 'string', 'max:50'],

            'description'    => ['sometimes', 'nullable', 'string', 'max:5000'],
            'is_active'      => ['sometimes', 'boolean'],
        ];
    }
}
