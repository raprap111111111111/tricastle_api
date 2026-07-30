<?php

namespace App\Http\Requests\v1\QualityScore;

use Illuminate\Foundation\Http\FormRequest;

class GetQualityScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('quality_score'));
    }

    public function rules(): array
    {
        return [];
    }
}