<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class RecentActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}