<?php

namespace App\Http\Requests\v1\OcrManualCorrection;

use App\Models\OcrManualCorrection;
use Illuminate\Foundation\Http\FormRequest;

class GetAllOcrManualCorrectionRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT = 10;
    private const MAX_LIMIT = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', OcrManualCorrection::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'order_by' => $this->getValidOrderBy(),
            'order_dir' => $this->getValidOrderDir(),
            'limit' => $this->getValidLimit(),
        ]);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'min:1', 'max:255'],

            'offset' => ['nullable', 'integer', 'min:0'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],

            'order_by' => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir' => ['nullable', 'in:asc,desc'],

            // Relationship Filters
            'ocr_job_id' => ['nullable', 'integer', 'exists:ocr_jobs,id'],
            'ocr_field_extraction_id' => ['nullable', 'integer', 'exists:ocr_field_extractions,id'],
            'applicant_document_id' => ['nullable', 'integer', 'exists:applicant_documents,id'],
            'ocr_template_id' => ['nullable', 'integer', 'exists:ocr_templates,id'],

            // Field Filters
            'field_name' => ['nullable', 'string', 'max:255'],
            'field_type' => ['nullable', 'string', 'max:100'],

            // Classification Filters
            'correction_type' => ['nullable', 'string', 'in:' . implode(',', OcrManualCorrection::CORRECTION_TYPES)],
            'correction_types' => ['nullable', 'array'],
            'correction_types.*' => ['string', 'in:' . implode(',', OcrManualCorrection::CORRECTION_TYPES)],
            'severity' => ['nullable', 'string', 'in:' . implode(',', OcrManualCorrection::SEVERITIES)],
            'severities' => ['nullable', 'array'],
            'severities.*' => ['string', 'in:' . implode(',', OcrManualCorrection::SEVERITIES)],

            // User Filters
            'corrected_by' => ['nullable', 'integer', 'exists:users,id'],
            'verified_by' => ['nullable', 'integer', 'exists:users,id'],
            'reviewed_by' => ['nullable', 'integer', 'exists:users,id'],

            // Context Filters
            'provider_used' => ['nullable', 'string', 'max:255'],
            'template_used' => ['nullable', 'string', 'max:255'],
            'error_pattern_id' => ['nullable', 'string', 'max:255'],
            'training_batch_id' => ['nullable', 'string', 'max:255'],

            // Boolean Filters
            'is_verified' => ['nullable', 'boolean'],
            'is_disputed' => ['nullable', 'boolean'],
            'used_for_training' => ['nullable', 'boolean'],
            'is_recurring_error' => ['nullable', 'boolean'],
            'was_critical_field' => ['nullable', 'boolean'],
            'improved_accuracy' => ['nullable', 'boolean'],
            'added_to_pattern_library' => ['nullable', 'boolean'],

            // Confidence Range (before)
            'min_confidence' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'max_confidence' => ['nullable', 'numeric', 'min:0', 'max:100', 'gte:min_confidence'],

            // Confidence Range (after)
            'min_confidence_after' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'max_confidence_after' => ['nullable', 'numeric', 'min:0', 'max:100', 'gte:min_confidence_after'],

            // Time Range
            'min_time_to_correct' => ['nullable', 'integer', 'min:0'],
            'max_time_to_correct' => ['nullable', 'integer', 'min:0', 'gte:min_time_to_correct'],

            // Occurrence
            'min_occurrence_count' => ['nullable', 'integer', 'min:1'],

            // Date Ranges — Correction
            'correction_from' => ['nullable', 'date'],
            'correction_to' => ['nullable', 'date', 'after_or_equal:correction_from'],

            // Date Ranges — Verified
            'verified_from' => ['nullable', 'date'],
            'verified_to' => ['nullable', 'date', 'after_or_equal:verified_from'],

            // Date Ranges — Trained
            'trained_from' => ['nullable', 'date'],
            'trained_to' => ['nullable', 'date', 'after_or_equal:trained_from'],

            // Date Ranges — Created
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date', 'after_or_equal:created_from'],
        ];
    }

    protected function getValidOrderBy(): string
    {
        return in_array($this->input('order_by'), $this->getValidColumns())
            ? $this->input('order_by')
            : self::DEFAULT_ORDER_BY;
    }

    protected function getValidOrderDir(): string
    {
        return in_array(strtolower($this->input('order_dir')), ['asc', 'desc'])
            ? strtolower($this->input('order_dir'))
            : self::DEFAULT_ORDER_DIR;
    }

    protected function getValidLimit(): int
    {
        return max(1, min(self::MAX_LIMIT, (int) $this->input('limit', self::DEFAULT_LIMIT)));
    }

    protected function getValidColumns(): array
    {
        return [
            'created_at',
            'updated_at',
            'field_name',
            'correction_type',
            'severity',
            'confidence_before',
            'confidence_after',
            'characters_changed',
            'edit_distance',
            'similarity_score',
            'occurrence_count',
            'time_to_correct_seconds',
            'verified_at',
            'correction_started_at',
            'correction_completed_at',
        ];
    }
}