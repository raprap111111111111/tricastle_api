<?php

namespace App\Http\Requests\v1\ApplicantBatch;

use Illuminate\Foundation\Http\FormRequest;

class RecordExamResultRequest extends FormRequest
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
            'exam_date'  => ['required', 'date', 'before_or_equal:today'],
            'exam_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'passed'     => ['required', 'boolean'],
        ];
    }
}