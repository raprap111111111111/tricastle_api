<?php

namespace App\Http\Requests\v1\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Role::class);
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description'   => ['nullable', 'string', 'max:255'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique'          => 'A role with this name already exists.',
            'permissions.*.exists' => 'One or more permissions do not exist.',
        ];
    }
}