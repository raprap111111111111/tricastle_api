<?php

// app/Http/Requests/v1/Setting/StoreSettingRequest.php

namespace App\Http\Requests\v1\Setting;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Setting::class);
    }

    public function rules(): array
    {
        return [
            'key'         => ['required', 'string', 'max:255', 'unique:settings,key'],
            'value'       => ['nullable', 'string'],
            'type'        => ['nullable', 'string', 'in:string,integer,boolean,json'],
            'group'       => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public'   => ['nullable', 'boolean'],
        ];
    }
}