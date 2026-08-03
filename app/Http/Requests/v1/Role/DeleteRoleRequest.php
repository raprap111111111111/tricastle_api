<?php
// app/Http/Requests/v1/Role/DeleteRoleRequest.php

namespace App\Http\Requests\v1\Role;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->route('role');

        return $role && $this->user()->can('delete', $role);
    }

    public function rules(): array
    {
        return [];
    }
}