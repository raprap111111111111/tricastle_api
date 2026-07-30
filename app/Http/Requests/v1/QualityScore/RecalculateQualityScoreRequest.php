<?php

namespace App\Http\Requests\v1\QualityScore;

use App\Models\QualityScore;
use Illuminate\Foundation\Http\FormRequest;

class RecalculateQualityScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recalculate', QualityScore::class);
    }

    public function rules(): array
    {
        return [
            'applicant_id' => ['required', 'integer', 'exists:applicants,id'],
        ];
    }
}