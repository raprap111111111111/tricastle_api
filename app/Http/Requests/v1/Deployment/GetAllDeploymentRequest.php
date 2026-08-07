<?php

namespace App\Http\Requests\v1\Deployment;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAllDeploymentRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'deployed_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Applicant::class);
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
            'search'    => ['nullable', 'string', 'max:100'],
            'offset'    => ['nullable', 'integer', 'min:0'],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'  => ['nullable', Rule::in($this->getValidColumns())],
            'order_dir' => ['nullable', Rule::in(['asc', 'desc'])],

            // ─── Filters ───────────────────────────────
            'country'   => ['nullable', 'string', 'max:100'],
            'company'   => ['nullable', 'string', 'max:200'],
            'batch_id'  => ['nullable', 'integer', 'exists:batches,id'],
            'status'    => ['nullable', 'string'],

            // ─── Date range ────────────────────────────
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],

            // ─── Include cancelled / all statuses ─────
            'include_all_statuses' => ['nullable', 'boolean'],
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
            'deployed_at',
            'deployment_country',
            'deployment_company',
            'contract_start_date',
            'contract_end_date',
            'flight_date',
            'created_at',
            'updated_at',
        ];
    }
}