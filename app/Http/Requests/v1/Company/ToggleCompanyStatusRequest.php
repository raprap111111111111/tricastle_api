<?php

namespace App\Http\Requests\v1\Company;

use Illuminate\Foundation\Http\FormRequest;

class ToggleCompanyStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'update',
            $this->route('company')
        );
    }

    public function rules(): array
    {
        return [];
    }
}