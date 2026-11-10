<?php

namespace App\Http\Requests\v1\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantRemarksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('applicant.update');
    }

    public function rules(): array
    {
        return [
            'remarks'   => ['present', 'nullable', 'array', 'max:50'],
            'remarks.*' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'remarks.array'      => 'Remarks must be an array list of items.',
            'remarks.max'        => 'You can add at most 50 remarks.',
            'remarks.*.required' => 'Each remark entry cannot be blank.',
            'remarks.*.max'      => 'Each remark entry must not exceed 255 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('remarks') && is_array($this->input('remarks'))) {
            $cleaned = collect($this->input('remarks'))
                ->map(fn ($item) => is_string($item) ? trim($item) : $item)
                ->filter(fn ($item) => is_string($item) && $item !== '')
                ->values()
                ->all();

            $this->merge(['remarks' => $cleaned]);
        }
    }
}