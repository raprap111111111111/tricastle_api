<?php
// app/Http/Requests/v1/SocialAccount/DeleteSocialAccountRequest.php

namespace App\Http\Requests\v1\SocialAccount;

use Illuminate\Foundation\Http\FormRequest;

class DeleteSocialAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('social_account'));
    }

    public function rules(): array
    {
        return [];
    }
}