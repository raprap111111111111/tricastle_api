<?php

namespace App\Http\Requests\v1\VerificationMismatch;

use App\Models\VerificationMismatch;
use Illuminate\Foundation\Http\FormRequest;

class GetAllVerificationMismatchRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', VerificationMismatch::class);
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
            'document_verification_id' => ['nullable', 'integer'],
            'severity'                 => ['nullable', 'in:low,moderate,critical'],
            'mismatch_type'            => ['nullable', 'in:value_mismatch,missing_in_document,missing_in_system,format_mismatch,date_mismatch'],
            'status'                   => ['nullable', 'in:open,correction_requested,corrected,waived,escalated'],
            'resolved_by'              => ['nullable', 'integer', 'exists:users,id'],

            // Special filters
            'unresolved_only'          => ['nullable', 'boolean'],
            'resolved_only'            => ['nullable', 'boolean'],
            'critical_only'            => ['nullable', 'boolean'],
            'escalated_only'           => ['nullable', 'boolean'],
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
            'field_name',
            'severity',
            'mismatch_type',
            'status',
            'resolved_at',
            'created_at',
            'updated_at',
        ];
    }
}