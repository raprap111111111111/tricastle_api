<?php

namespace App\Http\Requests\v1\ApplicantBatch;

use Illuminate\Foundation\Http\FormRequest;

class DeleteApplicantBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'delete',
            $this->route('applicant_batch')
        );
    }

    public function rules(): array
    {
        return [];
    }
}