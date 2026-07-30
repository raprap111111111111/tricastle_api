<?php
// app/Http/Requests/v1/SocialAccount/StoreSocialAccountRequest.php

namespace App\Http\Requests\v1\SocialAccount;

use App\Models\SocialAccount;
use Illuminate\Foundation\Http\FormRequest;

class StoreSocialAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', SocialAccount::class);
    }

    public function rules(): array
    {
        return [
            'user_id'          => ['required', 'integer', 'exists:users,id'],
            'provider'         => ['required', 'string', 'in:facebook,google,github'],
            'provider_id'      => ['required', 'string', 'max:255'],
            'avatar'           => ['nullable', 'string', 'max:255'],
            'token'            => ['nullable', 'string'],
            'refresh_token'    => ['nullable', 'string'],
            'token_expires_at' => ['nullable', 'date'],
        ];
    }
}