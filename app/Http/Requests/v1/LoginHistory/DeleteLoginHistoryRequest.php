<?php
// app/Http/Requests/v1/LoginHistory/DeleteLoginHistoryRequest.php

namespace App\Http\Requests\v1\LoginHistory;

use Illuminate\Foundation\Http\FormRequest;

class DeleteLoginHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('login_history'));
    }

    public function rules(): array
    {
        return [];
    }
}