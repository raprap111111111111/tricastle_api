<?php

namespace App\Http\Requests\v1\OcrFieldExtraction;

use App\Models\OcrFieldExtraction;
use Illuminate\Foundation\Http\FormRequest;

class GetAllOcrFieldExtractionRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', OcrFieldExtraction::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'order_by'  => $this->getValidOrderBy(),
            'order_dir' => $this->getValidOrderDir(),
            'limit'     => $this->getValidLimit(),
        ]);
    }

    public function rules(): array
    {
        return [
            // Search
            'search'                  => ['nullable', 'string', 'min:1', 'max:255'],

            // Pagination
            'offset'                  => ['nullable', 'integer', 'min:0'],
            'limit'                   => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],

            // Sorting
            'order_by'                => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'               => ['nullable', 'in:asc,desc'],

            // Filters
            'ocr_job_id'              => ['nullable', 'integer', 'exists:ocr_jobs,id'],
            'applicant_document_id'   => ['nullable', 'integer', 'exists:applicant_documents,id'],
            'field_name'              => ['nullable', 'string', 'max:255'],
            'field_type'              => ['nullable', 'string', 'max:100'],
            'field_category'          => ['nullable', 'string', 'max:100'],
            'status'                  => ['nullable', 'string', 'in:extracted,validated,requires_review,manually_corrected,accepted,rejected,missing,skipped,auto_filled'],
            'confidence_level'        => ['nullable', 'string', 'in:very_high,high,medium,low,very_low,unknown'],
            'source'                  => ['nullable', 'string', 'in:ocr,manual,api'],
            'is_required'             => ['nullable', 'boolean'],
            'is_primary_field'        => ['nullable', 'boolean'],
            'passed_validation'       => ['nullable', 'boolean'],
            'has_validation_errors'   => ['nullable', 'boolean'],
            'was_manually_corrected'  => ['nullable', 'boolean'],
            'matches_other_documents' => ['nullable', 'boolean'],
            'has_conflicts'           => ['nullable', 'boolean'],
            'has_typo_suggestions'    => ['nullable', 'boolean'],
            'has_final_value'         => ['nullable', 'boolean'],
            'unresolved'              => ['nullable', 'boolean'],
            'corrected_by'            => ['nullable', 'integer', 'exists:users,id'],
            'page_number'             => ['nullable', 'integer', 'min:1'],

            // Confidence range
            'min_confidence'          => ['nullable', 'numeric', 'min:0', 'max:100'],
            'max_confidence'          => ['nullable', 'numeric', 'min:0', 'max:100', 'gte:min_confidence'],

            // Correction count
            'min_correction_count'    => ['nullable', 'integer', 'min:0'],

            // Date ranges
            'corrected_from'          => ['nullable', 'date'],
            'corrected_to'            => ['nullable', 'date', 'after_or_equal:corrected_from'],
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
            'sort_order',
            'field_name',
            'confidence_score',
            'status',
            'correction_count',
            'corrected_at',
            'page_number',
        ];
    }
}