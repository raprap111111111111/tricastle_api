<?php
// app/Http/Requests/v1/SocialAccount/GetSocialAccountRequest.php

namespace App\Http\Requests\v1\SocialAccount;

use Illuminate\Foundation\Http\FormRequest;

class GetSocialAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('social_account'));
    }

    public function rules(): array
    {
        return [];
    }
}