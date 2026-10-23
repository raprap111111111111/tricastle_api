<?php

namespace App\Http\Requests\v1\PassportIssuingOffice;

use App\Models\PassportIssuingOffice;
use Illuminate\Foundation\Http\FormRequest;

class StorePassportIssuingOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', PassportIssuingOffice::class);
    }

    public function rules(): array
    {
        return [
            'region'     => ['required', 'string', 'max:50'],
            'name'       => ['required', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:50', 'unique:passport_issuing_offices,code'],
            'address'    => ['nullable', 'string', 'max:500'],
            'city'       => ['nullable', 'string', 'max:100'],
            'province'   => ['nullable', 'string', 'max:100'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ];
    }
}