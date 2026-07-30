<?php
// app/Http/Requests/v1/Notification/DeleteNotificationRequest.php

namespace App\Http\Requests\v1\Notification;

use Illuminate\Foundation\Http\FormRequest;

class DeleteNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('notification'));
    }

    public function rules(): array
    {
        return [];
    }
}