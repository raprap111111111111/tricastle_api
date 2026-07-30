<?php

namespace App\Http\Requests\v1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'confirmed',
                'different:current_password',
                'min:6', // or any minimum you want, or remove entirely
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.confirmed' => 'Password confirmation does not match.',
            'new_password.different' => 'New password must be different from current password.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.mixed' => 'Password must contain both uppercase and lowercase letters.',
            'new_password.numbers' => 'Password must contain at least one number.',
            'new_password.symbols' => 'Password must contain at least one symbol.',
        ];
    }
}
