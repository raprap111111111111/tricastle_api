<?php

namespace App\Http\Requests\v1\Company;

use Illuminate\Foundation\Http\FormRequest;

class GetCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'view',
            $this->route('company')
        );
    }

    public function rules(): array
    {
        return [];
    }
}