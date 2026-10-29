<?php

namespace App\Http\Requests\v1\BatchGroup;

use App\Models\BatchGroup;
use Illuminate\Foundation\Http\FormRequest;

class StoreBatchGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', BatchGroup::class);
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:100'],
            'color'     => ['nullable', 'string', 'max:50'],
            'max_slots' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
