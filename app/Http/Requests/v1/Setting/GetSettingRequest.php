<?php

// app/Http/Requests/v1/Setting/GetSettingRequest.php

namespace App\Http\Requests\v1\Setting;

use Illuminate\Foundation\Http\FormRequest;

class GetSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('setting'));
    }

    public function rules(): array
    {
        return [];
    }
}