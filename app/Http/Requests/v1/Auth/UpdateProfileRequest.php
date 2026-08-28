<?php

namespace App\Http\Requests\v1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'first_name'    => ['sometimes', 'required', 'string', 'max:255'],
            'middle_name'   => ['nullable', 'string', 'max:255'],
            'last_name'     => ['sometimes', 'required', 'string', 'max:255'],
            'suffix'        => ['nullable', 'string', 'max:50'],
            'email'         => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'phone'         => ['nullable', 'string', 'max:50'],
            'mobile'        => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'gender'        => ['nullable', 'string', 'in:male,female,other'],
            'department'    => ['nullable', 'string', 'max:255'],
            'position'      => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string'],
            'city'          => ['nullable', 'string', 'max:255'],
            'province'      => ['nullable', 'string', 'max:255'],
            'country'       => ['nullable', 'string', 'max:255'],
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'bio'           => ['nullable', 'string'],
            'avatar'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }
}