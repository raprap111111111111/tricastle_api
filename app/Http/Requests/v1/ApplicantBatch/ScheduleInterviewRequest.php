<?php

namespace App\Http\Requests\v1\ApplicantBatch;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleInterviewRequest extends FormRequest
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
            'interview_date'  => ['required', 'date', 'after_or_equal:today'],
            'interview_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}