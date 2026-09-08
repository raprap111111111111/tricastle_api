<?php

namespace App\Http\Requests\v1\Internship;

use Illuminate\Foundation\Http\FormRequest;

class ChangeCompanyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'receiving_company_id'  => ['required', 'integer', 'exists:companies,id'],
            'contract_start'        => ['required', 'date'],
            'change_reason'         => ['required', 'string', 'max:500'],
            'accepting_company_id'  => ['nullable', 'integer', 'exists:companies,id'],
            'batch_id'              => ['nullable', 'integer', 'exists:batches,id'],
            'internship_program_id' => ['nullable', 'integer', 'exists:internship_programs,id'],
            'contract_end'          => ['nullable', 'date', 'after_or_equal:contract_start'],
            'contract_years'        => ['nullable', 'integer', 'min:1', 'max:5'],
            'job_description'       => ['nullable', 'string', 'max:255'],
            'place_of_internship'   => ['nullable', 'string', 'max:255'],
            'stipend_amount'        => ['nullable', 'integer', 'min:0'],
            'meal_allowance_amount' => ['nullable', 'integer', 'min:0'],
        ];
    }
}