<?php

namespace App\Http\Requests\v1\Internship;

use Illuminate\Foundation\Http\FormRequest;

class SyncGuarantorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('guarantors') && is_array($this->input('guarantors'))) {
            $guarantors = array_map(function ($g) {
                if (isset($g['residence_cert_issued_at']) && $g['residence_cert_issued_at'] === '') {
                    $g['residence_cert_issued_at'] = null;
                }
                if (isset($g['age']) && ($g['age'] === '' || $g['age'] === null)) {
                    $g['age'] = null;
                }
                return $g;
            }, $this->input('guarantors'));

            $this->merge(['guarantors' => $guarantors]);
        }
    }

    public function rules(): array
    {
        return [
            'guarantors'                             => ['required', 'array', 'min:1', 'max:2'],
            'guarantors.*.full_name'                 => ['required', 'string', 'max:255'],
            'guarantors.*.age'                       => ['nullable', 'integer', 'min:18', 'max:100'],
            'guarantors.*.civil_status'              => ['nullable', 'string', 'max:50'],
            'guarantors.*.nationality'               => ['nullable', 'string', 'max:50'],
            'guarantors.*.address'                   => ['nullable', 'string'],
            'guarantors.*.residence_cert_no'         => ['nullable', 'string', 'max:50'],
            'guarantors.*.residence_cert_issued_at' => ['nullable', 'date'],
            'guarantors.*.residence_cert_place'      => ['nullable', 'string', 'max:120'],
            'guarantors.*.relationship'              => ['nullable', 'string', 'max:50'],
        ];
    }
}