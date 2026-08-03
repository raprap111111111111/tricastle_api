<?php
// app/Http/Requests/v1/Permission/StorePermissionRequest.php

namespace App\Http\Requests\v1\Permission;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Permission::class);
    }

    public function rules(): array
    {
        return [
            'name'        => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
                'regex:/^[a-z]+\.[a-zA-Z]+$/', // enforce dot notation: module.action
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