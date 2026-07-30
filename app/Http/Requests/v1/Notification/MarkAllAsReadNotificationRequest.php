<?php
// app/Http/Requests/v1/Notification/MarkAllAsReadNotificationRequest.php

namespace App\Http\Requests\v1\Notification;

use App\Models\Notification;
use Illuminate\Foundation\Http\FormRequest;

class MarkAllAsReadNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('markAllAsRead', Notification::class);
    }

    public function rules(): array
    {
        return [];
    }
}