<?php

namespace App\Http\Requests\v1\PassportIssuingOffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePassportIssuingOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $office = $this->route('passport_issuing_office');
        return $this->user()->can('update', $office);
    }

    public function rules(): array
    {
        $officeId = $this->route('passport_issuing_office')?->id;

        return [
            'region'     => ['sometimes', 'required', 'string', 'max:50'],
            'name'       => ['sometimes', 'required', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:50', Rule::unique('passport_issuing_offices', 'code')->ignore($officeId)],
            'address'    => ['nullable', 'string', 'max:500'],
            'city'       => ['nullable', 'string', 'max:100'],
            'province'   => ['nullable', 'string', 'max:100'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ];
    }
}