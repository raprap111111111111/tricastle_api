<?php

namespace App\Http\Requests\v1\PassportIssuingOffice;

use Illuminate\Foundation\Http\FormRequest;

class DeletePassportIssuingOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $office = $this->route('passport_issuing_office');
        return $this->user()->can('delete', $office);
    }

    public function rules(): array
    {
        return [];
    }
}