<?php

namespace App\Http\Requests\v1\ApplicantBatch;

use App\Enums\ApplicantBatchStatus;
use App\Models\ApplicantBatch;
use Illuminate\Foundation\Http\FormRequest;

class GetAllApplicantBatchRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'applied_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 15;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', ApplicantBatch::class);
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
            'search'        => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'        => ['nullable', 'integer', 'min:0'],
            'limit'         => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'      => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'     => ['nullable', 'in:asc,desc'],

            // Filters
            'applicant_id'  => ['nullable', 'integer', 'exists:applicants,id'],
            'batch_id'      => ['nullable', 'integer', 'exists:batches,id'],
            'status'        => ['nullable', 'in:' . implode(',', ApplicantBatchStatus::values())],
            'processed_by'  => ['nullable', 'integer', 'exists:users,id'],

            // Date filters
            'date_from'     => ['nullable', 'date'],
            'date_to'       => ['nullable', 'date', 'after_or_equal:date_from'],
            'recent_days'   => ['nullable', 'integer', 'min:1', 'max:365'],

            // Special filters
            'today'         => ['nullable', 'boolean'],
            'this_week'     => ['nullable', 'boolean'],
            'this_month'    => ['nullable', 'boolean'],
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
        return in_array(strtolower((string) $this->input('order_dir')), ['asc', 'desc'])
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
            'id',
            'applicant_id',
            'batch_id',
            'status',
            'applied_at',
            'interview_date',
            'medical_date',
            'exam_date',
            'accepted_at',
            'deployed_at',
            'exam_score',
            'created_at',
            'updated_at',
        ];
    }
}