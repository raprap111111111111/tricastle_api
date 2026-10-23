<?php

namespace App\Http\Requests\v1\PassportIssuingOffice;

use Illuminate\Foundation\Http\FormRequest;

class GetPassportIssuingOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $office = $this->route('passport_issuing_office');
        return $this->user()->can('view', $office);
    }

    public function rules(): array
    {
        return [];
    }
}