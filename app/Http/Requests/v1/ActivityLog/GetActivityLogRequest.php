<?php

namespace App\Http\Requests\v1\ActivityLog;

use Illuminate\Foundation\Http\FormRequest;

class GetActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('activity_log'));
    }

    public function rules(): array
    {
        return [];
    }
}