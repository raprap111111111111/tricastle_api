<?php

namespace App\Http\Requests\v1\CorrectionRequest;

use App\Models\CorrectionRequest;
use Illuminate\Foundation\Http\FormRequest;

class GetAllCorrectionRequestRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', CorrectionRequest::class);
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
            'search'                   => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'                   => ['nullable', 'integer', 'min:0'],
            'limit'                    => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'                 => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'                => ['nullable', 'in:asc,desc'],

            // Filters
            'status'                   => ['nullable', 'in:pending,under_review,approved,rejected,completed,cancelled'],
            'severity'                 => ['nullable', 'in:low,moderate,critical'],
            'requested_by'             => ['nullable', 'integer', 'exists:users,id'],
            'document_verification_id' => ['nullable', 'integer'],
            'applicant_document_id'    => ['nullable', 'integer'],
            'requires_approval'        => ['nullable', 'boolean'],
            'requires_new_document'    => ['nullable', 'boolean'],

            // Special filters
            'overdue_only'             => ['nullable', 'boolean'],
            'due_soon'                 => ['nullable', 'boolean'],
            'due_within_days'          => ['nullable', 'integer', 'min:1', 'max:30'],
            'critical_only'            => ['nullable', 'boolean'],
            'requires_approval_only'   => ['nullable', 'boolean'],
            'active_only'              => ['nullable', 'boolean'],
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
            'id',
            'request_code',
            'status',
            'severity',
            'due_date',
            'created_at',
            'updated_at',
        ];
    }
}