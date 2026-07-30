<?php

namespace App\Http\Requests\v1\ApplicantBatch;

use Illuminate\Foundation\Http\FormRequest;

class RejectApplicantBatchRequest extends FormRequest
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
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ];
    }
}