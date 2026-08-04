<?php

namespace App\Http\Requests\v1\ApplicantBatch;

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
                'required',
                'integer',
                'exists:applicants,id',
                // Must be final_list status to be assigned to a batch
                Rule::exists('applicants', 'id')->where(
                    fn ($q) => $q->where('status', 'final_list')
                ),
                // Prevent duplicate assignment to same batch
                Rule::unique('applicant_batches')
                    ->where(fn ($q) => $q->where('batch_id', $this->input('batch_id')))
                    ->whereNull('deleted_at'),
            ],
            'batch_id' => [
                'required',
                'integer',
                // Must be an active batch
                Rule::exists('batches', 'id')->where(
                    fn ($q) => $q->where('status', 'active')
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_id.exists'  => 'The applicant does not exist or is not in the final list yet.',
            'applicant_id.unique'  => 'This applicant has already been assigned to this batch.',
            'batch_id.exists'      => 'The selected batch does not exist or is not active.',
        ];
    }
}