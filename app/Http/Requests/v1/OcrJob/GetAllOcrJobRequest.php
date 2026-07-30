<?php

namespace App\Http\Requests\v1\OcrJob;

use App\Models\OcrJob;
use Illuminate\Foundation\Http\FormRequest;

class GetAllOcrJobRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT = 10;
    private const MAX_LIMIT = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', OcrJob::class);
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

            // Filters
            'status' => ['nullable', 'string', 'in:pending,queued,processing,completed,failed,partial,requires_review,cancelled,timeout,retrying'],
            'provider' => ['nullable', 'string', 'in:aws_textract,google_vision,azure_form_recognizer,tesseract,openai_vision,manual,custom_api'],
            'batch_id' => ['nullable', 'string', 'max:255'],
            'applicant_document_id' => ['nullable', 'integer', 'exists:applicant_documents,id'],
            'initiated_by' => ['nullable', 'integer', 'exists:users,id'],
            'reviewed_by' => ['nullable', 'integer', 'exists:users,id'],
            'detected_document_type' => ['nullable', 'string', 'max:255'],
            'is_document_type_matched' => ['nullable', 'boolean'],
            'is_blurry' => ['nullable', 'boolean'],
            'has_glare' => ['nullable', 'boolean'],
            'is_free_tier' => ['nullable', 'boolean'],
            'retryable' => ['nullable', 'boolean'],

            // Priority
            'priority' => ['nullable', 'integer', 'min:1', 'max:10'],
            'min_priority' => ['nullable', 'integer', 'min:1', 'max:10'],

            // Confidence range
            'min_confidence' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'max_confidence' => ['nullable', 'numeric', 'min:0', 'max:100', 'gte:min_confidence'],

            // Date ranges
            'queued_from' => ['nullable', 'date'],
            'queued_to' => ['nullable', 'date', 'after_or_equal:queued_from'],
            'completed_from' => ['nullable', 'date'],
            'completed_to' => ['nullable', 'date', 'after_or_equal:completed_from'],
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
            'job_code',
            'status',
            'priority',
            'overall_confidence',
            'processing_time_ms',
            'api_cost',
            'queued_at',
            'started_at',
            'completed_at',
        ];
    }
}