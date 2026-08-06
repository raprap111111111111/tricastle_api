<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class TrendsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'range' => ['nullable', 'string', 'in:7d,14d,30d'],
        ];
    }

    public function days(): int
    {
        return match ($this->input('range', '14d')) {
            '7d'  => 7,
            '30d' => 30,
            default => 14,
        };
    }
}