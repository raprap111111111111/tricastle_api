<?php

namespace App\Http\Requests\v1\CorrectionApproval;

use App\Models\CorrectionApproval;
use Illuminate\Foundation\Http\FormRequest;

class GetAllCorrectionApprovalRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', CorrectionApproval::class);
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
            'correction_request_id' => ['nullable', 'integer', 'exists:correction_requests,id'],
            'approver_id'           => ['nullable', 'integer', 'exists:users,id'],
            'decision'              => ['nullable', 'in:pending,approved,rejected,escalated'],
            'approval_level'        => ['nullable', 'integer', 'min:1'],

            // Special filters
            'pending_only'          => ['nullable', 'boolean'],
            'decided_only'          => ['nullable', 'boolean'],
            'supervisor_level'      => ['nullable', 'boolean'],
            'admin_level'           => ['nullable', 'boolean'],
            'level'                 => ['nullable', 'integer', 'min:1'],
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
            'decision',
            'approval_level',
            'decided_at',
            'created_at',
            'updated_at',
        ];
    }
}