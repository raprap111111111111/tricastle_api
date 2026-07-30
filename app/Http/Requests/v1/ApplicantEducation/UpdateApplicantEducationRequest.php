<?php

namespace App\Http\Requests\v1\ApplicantEducation;

use App\Domain\ApplicantEducation\Enums\EducationLevel;
use App\Domain\ApplicantEducation\Enums\EducationStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'update',
            $this->route('applicant_education')
        );
    }

    public function rules(): array
    {
        $maxYear = (int) date('Y') + 10;
        $minYear = 1900;

        return [
            'education_level'  => ['sometimes', 'required', 'in:' . implode(',', EducationLevel::values())],
            'education_status' => ['sometimes', 'in:' . implode(',', EducationStatus::values())],

            'school_name'      => ['sometimes', 'required', 'string', 'max:255'],
            'course'           => ['sometimes', 'nullable', 'string', 'max:255'],

            'year_started'     => ['sometimes', 'nullable', 'integer', "min:{$minYear}", "max:{$maxYear}"],
            'year_ended'       => [
                'sometimes', 'nullable', 'integer', "min:{$minYear}", "max:{$maxYear}",
                'gte:year_started',
            ],

            'honors'           => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'year_ended.gte' => 'Year ended must be greater than or equal to year started.',
        ];
    }
}