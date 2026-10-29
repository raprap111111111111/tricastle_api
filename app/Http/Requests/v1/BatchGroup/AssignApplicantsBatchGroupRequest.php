<?php

namespace App\Http\Requests\v1\BatchGroup;

use Illuminate\Foundation\Http\FormRequest;

class AssignApplicantsBatchGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        $group = $this->route('group');
        return $group ? $this->user()->can('assign', $group) : false;
    }

    public function rules(): array
    {
        return [
            'applicant_batch_ids'   => ['required', 'array', 'min:1'],
            'applicant_batch_ids.*' => ['integer', 'exists:applicant_batches,id'],
        ];
    }
}
