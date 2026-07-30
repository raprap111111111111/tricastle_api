<?php

namespace App\Http\Requests\v1\QualityScore;

use Illuminate\Foundation\Http\FormRequest;

class DeleteQualityScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('quality_score'));
    }

    public function rules(): array
    {
        return [];
    }
}