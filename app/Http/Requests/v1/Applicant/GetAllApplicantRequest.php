<?php

namespace App\Http\Requests\v1\Applicant;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAllApplicantRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'id';
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
            'search'    => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'    => ['nullable', 'integer', 'min:0'],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'  => ['nullable', Rule::in($this->getValidColumns())],
            'order_dir' => ['nullable', Rule::in(['asc', 'desc'])],

            // ─── Filters ─────────────────────────────────
            'status'             => ['nullable', 'string'],
            'exclude_statuses'   => ['nullable', 'string'],
            'gender'             => ['nullable', 'in:male,female'],
            'civil_status'       => ['nullable', 'string'],
            'nationality'        => ['nullable', 'string'],
            'quality_grade'      => ['nullable', 'in:A,B,C,D,F'],
            'assigned_staff_id'  => ['nullable', 'integer', 'exists:users,id'],

            // ─── Batch filters ───────────────────────────
            'batch_id'           => ['nullable', 'integer', 'exists:batches,id'],
            'batch_status'       => ['nullable', 'string'],

            // ─── 🗺️ Location filters (NEW) ──────────────
            'city'               => ['nullable', 'string', 'max:100'],
            'province'           => ['nullable', 'string', 'max:100'],
            'address'            => ['nullable', 'string', 'max:200'],

            // ─── Special filters ─────────────────────────
            'passport_expiring_within_months' => ['nullable', 'integer', 'min:1', 'max:24'],
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
            'applicant_code',
            'first_name',
            'last_name',
            'email',
            'status',
            'quality_score',
            'quality_grade',
            'passport_expiry',
            'created_at',
            'updated_at',
        ];
    }
}