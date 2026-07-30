<?php
// app/Http/Requests/v1/Notification/MarkAsReadNotificationRequest.php

namespace App\Http\Requests\v1\Notification;

use Illuminate\Foundation\Http\FormRequest;

class MarkAsReadNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('markAsRead', $this->route('notification'));
    }

    public function rules(): array
    {
        return [];
    }
}