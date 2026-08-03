<?php
// app/Http/Requests/v1/Permission/UpdatePermissionRequest.php

namespace App\Http\Requests\v1\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->route('permission');

        return $permission && $this->user()->can('update', $permission);
    }

    public function rules(): array
    {
        $permissionId = $this->route('permission')?->id;

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z]+\.[a-zA-Z]+$/',
                Rule::unique('permissions', 'name')->ignore($permissionId),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'module'      => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A permission with this name already exists.',
            'name.regex'  => 'Permission name must be in dot notation (e.g., "role.viewAny").',
        ];
    }
}