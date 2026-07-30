<?php

namespace App\Http\Requests\v1\QualityScore;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQualityScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('quality_score'));
    }

    public function rules(): array
    {
        return [
            'overall_score'       => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'completeness_score'  => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'accuracy_score'      => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'consistency_score'   => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'timeliness_score'    => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'total_documents'     => ['sometimes', 'integer', 'min:0'],
            'verified_documents'  => ['sometimes', 'integer', 'min:0'],
            'rejected_documents'  => ['sometimes', 'integer', 'min:0'],
            'pending_documents'   => ['sometimes', 'integer', 'min:0'],
            'total_mismatches'    => ['sometimes', 'integer', 'min:0'],
            'critical_mismatches' => ['sometimes', 'integer', 'min:0'],
            'open_corrections'    => ['sometimes', 'integer', 'min:0'],
            'breakdown'           => ['nullable', 'array'],
        ];
    }
}