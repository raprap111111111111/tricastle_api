<?php

namespace App\Http\Requests\v1\CompanyCategory;

use Illuminate\Foundation\Http\FormRequest;

class GetCompanyCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'view',
            $this->route('company_category')
        );
    }

    public function rules(): array
    {
        return [];
    }
}