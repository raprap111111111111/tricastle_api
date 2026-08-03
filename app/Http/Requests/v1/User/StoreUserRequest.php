<?php
// app/Http/Requests/v1/User/StoreUserRequest.php

namespace App\Http\Requests\v1\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    public function rules(): array
    {
        return [
            // Personal
            'first_name'    => ['required', 'string', 'max:100'],
            'middle_name'   => ['nullable', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'suffix'        => ['nullable', 'string', 'max:20'],

            // Contact
            'email'         => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'mobile'        => ['nullable', 'string', 'max:20'],

            // Auth
            'password'      => ['required', 'confirmed', Password::defaults()],

            // Avatar (file)
            'avatar'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Employment
            'employee_code' => ['nullable', 'string', 'max:50', 'unique:users,employee_code'],
            'department'    => ['nullable', 'string', 'max:100'],
            'position'      => ['nullable', 'string', 'max:100'],

            // Role (single role — matches DTO)
            'role'          => ['nullable', 'string', 'exists:roles,name'],

            // Status
            'is_active'     => ['nullable', 'boolean'],
        ];
    }
}