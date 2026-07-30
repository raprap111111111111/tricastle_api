<?php

namespace App\Http\Requests\v1\CompanyCategory;

use App\Models\CompanyCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', CompanyCategory::class);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', 'unique:company_categories,name'],
            'slug'        => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:company_categories,slug'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A company category with this name already exists.',
            'slug.unique' => 'A company category with this slug already exists.',
        ];
    }
}