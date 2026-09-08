<?php

namespace App\Http\Requests\v1\Internship;

use Illuminate\Foundation\Http\FormRequest;

class GenerateMoaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'agreement_date'        => ['nullable', 'date'],
            'municipality'          => ['nullable', 'string', 'max:120'],
            'contract_start'        => ['nullable', 'date'],
            'contract_end'          => ['nullable', 'date'],
            'contract_years'        => ['nullable', 'integer', 'min:1', 'max:5'],
            'job_description'       => ['nullable', 'string', 'max:255'],
            'place_of_internship'   => ['nullable', 'string', 'max:255'],
            'stipend_amount'        => ['nullable', 'integer', 'min:0'],
            'meal_allowance_amount' => ['nullable', 'integer', 'min:0'],
        ];
    }
}