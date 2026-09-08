<?php

namespace App\Http\Requests\v1\Internship;

use Illuminate\Foundation\Http\FormRequest;

class BulkGenerateMoaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'internship_ids'        => ['nullable', 'array'],
            'internship_ids.*'      => ['integer', 'exists:applicant_internships,id'],
            'batch_id'              => ['nullable', 'integer', 'exists:batches,id'],
            'only_current'          => ['nullable', 'boolean'],
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('internship_ids') && ! $this->filled('batch_id')) {
                $validator->errors()->add('internship_ids', 'Provide internship_ids or batch_id.');
            }
        });
    }
}