<?php

namespace App\Http\Requests\v1\Internship;

use App\Enums\InternshipStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInternshipRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'batch_id'               => ['sometimes', 'nullable', 'integer', 'exists:batches,id'],
            'internship_program_id'  => ['sometimes', 'nullable', 'integer', 'exists:internship_programs,id'],
            'program_type'           => ['sometimes', Rule::in(['titp', 'ssw', 'ssw_transfer'])],
            'status'                 => ['sometimes', Rule::in(array_column(InternshipStatus::cases(), 'value'))],
            'dispatching_company_id' => ['sometimes', 'nullable', 'integer', 'exists:companies,id'],
            'accepting_company_id'   => ['sometimes', 'nullable', 'integer', 'exists:companies,id'],
            'receiving_company_id'   => ['sometimes', 'nullable', 'integer', 'exists:companies,id'],
            'job_description'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'place_of_internship'    => ['sometimes', 'nullable', 'string', 'max:255'],
            'municipality'           => ['sometimes', 'nullable', 'string', 'max:120'],
            'agreement_date'         => ['sometimes', 'nullable', 'date'],
            'contract_start'         => ['sometimes', 'nullable', 'date'],
            'contract_end'           => ['sometimes', 'nullable', 'date'],
            'contract_years'         => ['sometimes', 'nullable', 'integer', 'min:1', 'max:5'],
            'stipend_amount'         => ['sometimes', 'nullable', 'integer', 'min:0'],
            'meal_allowance_amount'  => ['sometimes', 'nullable', 'integer', 'min:0'],
            'work_days'              => ['sometimes', 'nullable', 'string', 'max:100'],
            'day_off'                => ['sometimes', 'nullable', 'string', 'max:50'],
            'time_start'             => ['sometimes', 'nullable', 'string', 'max:30'],
            'time_end'               => ['sometimes', 'nullable', 'string', 'max:30'],
            'lunch_break'            => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }
}