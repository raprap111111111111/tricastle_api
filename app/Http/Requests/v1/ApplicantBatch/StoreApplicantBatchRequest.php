<?php

namespace App\Http\Requests\v1\ApplicantBatch;

use App\Domain\ApplicantBatch\Enums\ApplicantBatchStatus;
use App\Models\ApplicantBatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicantBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ApplicantBatch::class);
    }

    public function rules(): array
    {
        return [
            'applicant_id' => [
                'required', 'integer', 'exists:applicants,id',
                Rule::unique('applicant_batches')
                    ->where(fn ($q) => $q->where('batch_id', $this->input('batch_id')))
                    ->whereNull('deleted_at'),
            ],
            'batch_id'     => ['required', 'integer', 'exists:batches,id'],
            'status'       => ['nullable', 'in:' . implode(',', ApplicantBatchStatus::values())],
            'applied_at'   => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_id.unique' => 'This applicant has already applied to this batch.',
            'applicant_id.exists' => 'The selected applicant does not exist.',
            'batch_id.exists'     => 'The selected batch does not exist.',
        ];
    }
}