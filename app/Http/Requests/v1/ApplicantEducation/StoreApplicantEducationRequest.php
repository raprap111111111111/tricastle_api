<?php

namespace App\Http\Requests\v1\ApplicantEducation;

use App\Enums\EducationLevel;
use App\Enums\EducationStatus;
use App\Models\ApplicantEducation;
use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ApplicantEducation::class);
    }

    public function rules(): array
    {
        $maxYear = (int) date('Y') + 10; // allow up to 10 years ahead (for ongoing)
        $minYear = 1900;

        return [
            'applicant_id'     => ['required', 'integer', 'exists:applicants,id'],

            'education_level'  => ['required', 'in:' . implode(',', EducationLevel::values())],
            'education_status' => ['nullable', 'in:' . implode(',', EducationStatus::values())],

            'school_name'      => ['required', 'string', 'max:255'],
            'course'           => ['nullable', 'string', 'max:255'],

            'year_started'     => ['nullable', 'integer', "min:{$minYear}", "max:{$maxYear}"],
            'year_ended'       => [
                'nullable', 'integer', "min:{$minYear}", "max:{$maxYear}",
                'gte:year_started',
            ],

            'honors'           => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_id.exists' => 'The selected applicant does not exist.',
            'year_ended.gte'      => 'Year ended must be greater than or equal to year started.',
        ];
    }
}