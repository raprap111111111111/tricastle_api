<?php

namespace App\Http\Requests\v1\ActivityLog;

use Illuminate\Foundation\Http\FormRequest;

class GetActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Spatie doesn't use policies — allow authenticated users
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }
}