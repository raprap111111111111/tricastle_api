<?php

namespace App\Http\Requests\v1\BatchGroup;

use App\Models\BatchGroup;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        $group = $this->route('group');
        return $group ? $this->user()->can('update', $group) : false;
    }

    public function rules(): array
    {
        return [
            'name'      => ['sometimes', 'required', 'string', 'max:100'],
            'color'     => ['nullable', 'string', 'max:50'],
            'max_slots' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
