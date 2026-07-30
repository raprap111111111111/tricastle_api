<?php
// app/Http/Requests/v1/Notification/GetNotificationRequest.php

namespace App\Http\Requests\v1\Notification;

use App\Models\Notification;
use Illuminate\Foundation\Http\FormRequest;

class GetNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('notification'));
    }

    public function rules(): array
    {
        return [];
    }
}