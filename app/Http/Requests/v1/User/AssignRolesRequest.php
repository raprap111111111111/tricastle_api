<?php
// app/Http/Requests/v1/User/AssignRolesRequest.php

namespace App\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;

class AssignRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('assignRoles', $this->route('user'));
    }

    public function rules(): array
    {
        return [
            'roles'   => ['required', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ];
    }
}