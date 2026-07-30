<?php

// app/Http/Requests/v1/Setting/UpdateSettingRequest.php

namespace App\Http\Requests\v1\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('setting'));
    }

    public function rules(): array
    {
        $settingId = $this->route('setting')?->id;

        return [
            'key'         => ['nullable', 'string', 'max:255', Rule::unique('settings', 'key')->ignore($settingId)],
            'value'       => ['nullable', 'string'],
            'type'        => ['nullable', 'string', 'in:string,integer,boolean,json'],
            'group'       => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public'   => ['nullable', 'boolean'],
        ];
    }
}