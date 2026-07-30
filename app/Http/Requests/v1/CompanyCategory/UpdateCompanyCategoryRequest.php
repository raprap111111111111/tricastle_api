<?php

namespace App\Http\Requests\v1\CompanyCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $category = $this->route('company_category');
        return $this->user()->can('update', $category);
    }

    public function rules(): array
    {
        $categoryId = $this->route('company_category')?->id;

        return [
            'name' => [
                'sometimes', 'required', 'string', 'max:255',
                Rule::unique('company_categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'sometimes', 'nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('company_categories', 'slug')->ignore($categoryId),
            ],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}