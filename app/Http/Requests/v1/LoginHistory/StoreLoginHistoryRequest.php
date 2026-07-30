<?php
// app/Http/Requests/v1/LoginHistory/StoreLoginHistoryRequest.php

namespace App\Http\Requests\v1\LoginHistory;

use App\Models\LoginHistory;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoginHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', LoginHistory::class);
    }

    public function rules(): array
    {
        return [
            'user_id'        => ['required', 'integer', 'exists:users,id'],
            'ip_address'     => ['nullable', 'string', 'max:45'],
            'user_agent'     => ['nullable', 'string', 'max:255'],
            'device_type'    => ['nullable', 'string', 'max:50'],
            'browser'        => ['nullable', 'string', 'max:100'],
            'platform'       => ['nullable', 'string', 'max:100'],
            'location'       => ['nullable', 'string', 'max:255'],
            'status'         => ['required', 'in:success,failed,blocked'],
            'failure_reason' => ['nullable', 'string', 'max:255'],
            'login_method'   => ['required', 'string', 'in:email,facebook'],
            'logged_in_at'   => ['nullable', 'date'],
        ];
    }
}