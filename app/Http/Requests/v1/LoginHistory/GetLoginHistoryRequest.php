<?php
// app/Http/Requests/v1/LoginHistory/GetLoginHistoryRequest.php

namespace App\Http\Requests\v1\LoginHistory;

use Illuminate\Foundation\Http\FormRequest;

class GetLoginHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('login_history'));
    }

    public function rules(): array
    {
        return [];
    }
}