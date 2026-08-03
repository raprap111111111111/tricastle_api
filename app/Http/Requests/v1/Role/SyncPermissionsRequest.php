<?php
// app/Http/Requests/v1/Role/SyncPermissionsRequest.php

namespace App\Http\Requests\v1\Role;

use Illuminate\Foundation\Http\FormRequest;

class SyncPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->route('role');

        return $role && $this->user()->can('managePermissions', $role);
    }

    public function rules(): array
    {
        return [
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.required' => 'Permissions list is required.',
            'permissions.*.exists' => 'One or more permissions do not exist.',
        ];
    }
}