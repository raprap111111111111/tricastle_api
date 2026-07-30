<?php

namespace App\Http\Requests\v1\DocumentVerification;

use App\Models\DocumentVerification;
use Illuminate\Foundation\Http\FormRequest;

class GetAllDocumentVerificationRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', DocumentVerification::class);
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
            'search'                => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'                => ['nullable', 'integer', 'min:0'],
            'limit'                 => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'              => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'             => ['nullable', 'in:asc,desc'],

            // Filters
            'status'                => ['nullable', 'in:pending,in_progress,completed,requires_correction,approved,rejected'],
            'verified_by'           => ['nullable', 'integer', 'exists:users,id'],
            'reviewed_by'           => ['nullable', 'integer', 'exists:users,id'],
            'applicant_document_id' => ['nullable', 'integer', 'exists:applicant_documents,id'],

            // Special filters
            'pending_only'          => ['nullable', 'boolean'],
            'in_progress_only'      => ['nullable', 'boolean'],
            'min_match_percentage'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'max_match_percentage'  => ['nullable', 'numeric', 'min:0', 'max:100'],
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
            'status',
            'match_percentage',
            'total_fields',
            'matched_fields',
            'mismatched_fields',
            'missing_fields',
            'started_at',
            'completed_at',
            'created_at',
            'updated_at',
        ];
    }
}