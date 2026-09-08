<?php

namespace App\Http\Requests\v1\Internship;

use App\Enums\InternshipProgramType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInternshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'applicant_id'            => ['required', 'integer', 'exists:applicants,id'],
            'batch_id'                => ['nullable', 'integer', 'exists:batches,id'],
            'internship_program_id'   => ['nullable', 'integer', 'exists:internship_programs,id'],
            'program_type'            => ['nullable', Rule::enum(InternshipProgramType::class)],
            'dispatching_company_id'  => ['nullable', 'integer', 'exists:companies,id'],
            'accepting_company_id'    => ['nullable', 'integer', 'exists:companies,id'],
            'receiving_company_id'    => ['nullable', 'integer', 'exists:companies,id'],
            'job_description'         => ['nullable', 'string', 'max:255'],
            'place_of_internship'     => ['nullable', 'string', 'max:255'],
            'municipality'            => ['nullable', 'string', 'max:120'],
            'agreement_date'          => ['nullable', 'date'],
            'contract_start'          => ['nullable', 'date'],
            'contract_end'            => ['nullable', 'date', 'after_or_equal:contract_start'],
            'contract_years'          => ['nullable', 'integer', 'min:1', 'max:5'],
            'stipend_amount'          => ['nullable', 'integer', 'min:0'],
            'meal_allowance_amount'   => ['nullable', 'integer', 'min:0'],
            'work_days'               => ['nullable', 'string', 'max:100'],
            'day_off'                 => ['nullable', 'string', 'max:50'],
            'time_start'              => ['nullable', 'string', 'max:30'],
            'time_end'                => ['nullable', 'string', 'max:30'],
            'lunch_break'             => ['nullable', 'string', 'max:50'],
            'guarantors'              => ['nullable', 'array', 'max:2'],
            'guarantors.*.full_name'  => ['required_with:guarantors', 'string', 'max:255'],
            'guarantors.*.age'        => ['nullable', 'integer', 'min:18', 'max:100'],
            'guarantors.*.civil_status' => ['nullable', 'string', 'max:50'],
            'guarantors.*.nationality'=> ['nullable', 'string', 'max:50'],
            'guarantors.*.address'    => ['nullable', 'string'],
            'guarantors.*.residence_cert_no' => ['nullable', 'string', 'max:50'],
            'guarantors.*.residence_cert_issued_at' => ['nullable', 'date'],
            'guarantors.*.residence_cert_place' => ['nullable', 'string', 'max:120'],
            'guarantors.*.relationship' => ['nullable', 'string', 'max:50'],
        ];
    }
}