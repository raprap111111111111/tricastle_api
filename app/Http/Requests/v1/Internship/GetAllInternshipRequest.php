<?php

namespace App\Http\Requests\v1\Internship;

use Illuminate\Foundation\Http\FormRequest;

class GetAllInternshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('only_current')) {
            $val = $this->input('only_current');
            if ($val === 'false' || $val === '0' || $val === false || $val === 0) {
                $merge['only_current'] = false;
            } elseif ($val === 'true' || $val === '1' || $val === true || $val === 1) {
                $merge['only_current'] = true;
            }
        }

        if ($this->filled('applicant_id')) {
            $merge['applicant_id'] = (int) $this->input('applicant_id');
        }

        if ($this->filled('batch_id')) {
            $merge['batch_id'] = (int) $this->input('batch_id');
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'search'                 => ['nullable', 'string'],
            'status'                 => ['nullable', 'string'],
            'program_type'           => ['nullable', 'string'],
            'applicant_id'           => ['nullable', 'integer'],
            'batch_id'               => ['nullable', 'integer'],
            'internship_program_id'  => ['nullable', 'integer'],
            'receiving_company_id'   => ['nullable', 'integer'],
            'accepting_company_id'   => ['nullable', 'integer'],
            'dispatching_company_id' => ['nullable', 'integer'],
            'only_current'           => ['nullable', 'boolean'],
            'contract_start_from'    => ['nullable', 'date'],
            'contract_start_to'      => ['nullable', 'date'],
            'per_page'               => ['nullable', 'integer', 'min:1', 'max:100'],
            'limit'                  => ['nullable', 'integer', 'min:1', 'max:100'],
            'offset'                 => ['nullable', 'integer', 'min:0'],
            'page'                   => ['nullable', 'integer', 'min:1'],
            'sort_by'                => ['nullable', 'string'],
            'sort_direction'         => ['nullable', 'in:asc,desc'],
            'order_by'               => ['nullable', 'string'],
            'order_dir'              => ['nullable', 'in:asc,desc'],
        ];
    }
}