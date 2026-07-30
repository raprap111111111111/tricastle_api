<?php

namespace App\Http\Requests\v1\QualityScore;

use App\Models\QualityScore;
use Illuminate\Foundation\Http\FormRequest;

class StoreQualityScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', QualityScore::class);
    }

    public function rules(): array
    {
        return [
            'applicant_id'        => ['required', 'integer', 'exists:applicants,id'],
            'overall_score'       => ['required', 'numeric', 'min:0', 'max:100'],
            'completeness_score'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'accuracy_score'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'consistency_score'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'timeliness_score'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'total_documents'     => ['nullable', 'integer', 'min:0'],
            'verified_documents'  => ['nullable', 'integer', 'min:0'],
            'rejected_documents'  => ['nullable', 'integer', 'min:0'],
            'pending_documents'   => ['nullable', 'integer', 'min:0'],
            'total_mismatches'    => ['nullable', 'integer', 'min:0'],
            'critical_mismatches' => ['nullable', 'integer', 'min:0'],
            'open_corrections'    => ['nullable', 'integer', 'min:0'],
            'breakdown'           => ['nullable', 'array'],
        ];
    }
}