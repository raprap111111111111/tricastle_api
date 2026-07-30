<?php

namespace App\Http\Requests\v1\OcrFieldExtraction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOcrFieldExtractionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('ocr_field_extraction'));
    }

    public function rules(): array
    {
        return [
            'normalized_value'      => ['nullable', 'string'],
            'validated_value'       => ['nullable', 'string'],
            'display_value'         => ['nullable', 'string'],
            'confidence_score'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'confidence_level'      => ['nullable', 'in:very_high,high,medium,low,very_low,unknown'],
            'passed_validation'     => ['nullable', 'boolean'],
            'has_validation_errors' => ['nullable', 'boolean'],
            'validation_errors'     => ['nullable', 'string'],
            'validation_rule_used'  => ['nullable', 'string', 'max:255'],
            'validation_details'    => ['nullable', 'array'],
            'status'                => ['nullable', 'in:extracted,validated,requires_review,manually_corrected,accepted,rejected,missing,skipped,auto_filled'],
            'sort_order'            => ['nullable', 'integer', 'min:0'],
            'notes'                 => ['nullable', 'string', 'max:1000'],
            'metadata'              => ['nullable', 'array'],
        ];
    }
}