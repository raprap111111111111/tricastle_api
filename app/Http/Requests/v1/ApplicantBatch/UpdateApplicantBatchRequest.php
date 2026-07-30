<?php

namespace App\Http\Requests\v1\ApplicantBatch;

use App\Enums\ApplicantBatchStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'update',
            $this->route('applicant_batch')
        );
    }

    public function rules(): array
    {
        return [
            'status'           => ['sometimes', 'in:' . implode(',', ApplicantBatchStatus::values())],
            'interview_date'   => ['sometimes', 'nullable', 'date'],
            'medical_date'     => ['sometimes', 'nullable', 'date'],
            'exam_date'        => ['sometimes', 'nullable', 'date'],
            'accepted_at'      => ['sometimes', 'nullable', 'date'],
            'deployed_at'      => ['sometimes', 'nullable', 'date'],
            'exam_score'       => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
            'interview_notes'  => ['sometimes', 'nullable', 'string', 'max:5000'],
            'medical_notes'    => ['sometimes', 'nullable', 'string', 'max:5000'],
            'rejection_reason' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}