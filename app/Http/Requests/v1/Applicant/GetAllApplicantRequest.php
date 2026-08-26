<?php

namespace App\Http\Requests\v1\Applicant;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAllApplicantRequest extends FormRequest
{
    // 🎯 Change DEFAULT_ORDER_BY to 'created_at' (matches your composite index!)
    private const DEFAULT_ORDER_BY  = 'created_at';
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
            'page'      => max(1, (int) $this->input('page', 1)),
        ]);
    }

    public function rules(): array
    {
        return [
            'page'      => ['nullable', 'integer', 'min:1'],
            'search'    => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'    => ['nullable', 'integer', 'min:0'],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'  => ['nullable', Rule::in($this->getValidColumns())],
            'order_dir' => ['nullable', Rule::in(['asc', 'desc'])],

            // Filters
            'status'             => ['nullable', 'string'],
            'exclude_statuses'   => ['nullable', 'string'],
            'gender'             => ['nullable', 'in:male,female'],
            'civil_status'       => ['nullable', 'string'],
            'nationality'        => ['nullable', 'string'],
            'quality_grade'      => ['nullable', 'in:A,B,C,D,F'],
            'assigned_staff_id'  => ['nullable', 'integer', 'exists:users,id'],
            'batch_id'           => ['nullable', 'integer', 'exists:batches,id'],
            'city'               => ['nullable', 'string', 'max:100'],
            'province'           => ['nullable', 'string', 'max:100'],
            'address'            => ['nullable', 'string', 'max:200'],
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