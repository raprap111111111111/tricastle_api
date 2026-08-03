<?php
// app/Http/Requests/v1/Role/GetRoleRequest.php

namespace App\Http\Requests\v1\Role;

use Illuminate\Foundation\Http\FormRequest;

class GetRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->route('role');

        return $role && $this->user()->can('view', $role);
    }

    public function rules(): array
    {
        return [];
    }
}