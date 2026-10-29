<?php

namespace App\Http\Requests\v1\Portal;

use Illuminate\Foundation\Http\FormRequest;

class VerifyClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Auto-sanitize inputs before validation runs
     */
    protected function prepareForValidation(): void
    {
        $sanitize = fn(?string $val) => $val ? preg_replace('/\s+/', ' ', trim($val)) : null;

        $this->merge([
            'first_name'    => $sanitize($this->first_name),
            'middle_name'   => $sanitize($this->middle_name),
            'last_name'     => $sanitize($this->last_name),
            'date_of_birth' => trim($this->date_of_birth ?? ''),
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name'    => ['required', 'string', 'max:100'],
            'middle_name'   => ['nullable', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date'],
        ];
    }
}