<?php
// app/Http/Requests/v1/User/UpdateUserRequest.php

namespace App\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'first_name'    => ['sometimes', 'required', 'string', 'max:100'],
            'middle_name'   => ['nullable', 'string', 'max:100'],
            'last_name'     => ['sometimes', 'required', 'string', 'max:100'],
            'suffix'        => ['nullable', 'string', 'max:20'],

            'email' => [
                'sometimes', 'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone'  => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],

            'password' => ['sometimes', 'nullable', 'confirmed', Password::defaults()],

            'avatar'        => ['nullable', 'string', 'max:500'],
            'bio'           => ['nullable', 'string', 'max:1000'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender'        => ['nullable', 'in:male,female,other'],

            'employee_code' => [
                'nullable', 'string', 'max:50',
                Rule::unique('users', 'employee_code')->ignore($userId),
            ],
            'department'    => ['nullable', 'string', 'max:100'],
            'position'      => ['nullable', 'string', 'max:100'],
            'hired_date'    => ['nullable', 'date'],
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id', "not_in:$userId"],

            'address'     => ['nullable', 'string', 'max:500'],
            'city'        => ['nullable', 'string', 'max:100'],
            'province'    => ['nullable', 'string', 'max:100'],
            'country'     => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],

            'is_active' => ['nullable', 'boolean'],
            'locale'    => ['nullable', 'string', 'max:10'],
            'timezone'  => ['nullable', 'string', 'max:100'],
            'theme'     => ['nullable', 'in:light,dark'],

            'roles'   => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}