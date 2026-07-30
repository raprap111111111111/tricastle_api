<?php

namespace App\Http\Requests\v1\Company;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class GetAllCompanyRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 15;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Company::class);
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
            'search'      => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'      => ['nullable', 'integer', 'min:0'],
            'limit'       => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'    => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'   => ['nullable', 'in:asc,desc'],

            // Filters
            'category_id' => ['nullable', 'integer', 'exists:company_categories,id'],
            'prefecture'  => ['nullable', 'string', 'max:100'],
            'city'        => ['nullable', 'string', 'max:100'],
            'country'     => ['nullable', 'string', 'max:100'],
            'is_active'   => ['nullable', 'boolean'],

            // Date filters
            'date_from'   => ['nullable', 'date'],
            'date_to'     => ['nullable', 'date', 'after_or_equal:date_from'],
            'recent_days' => ['nullable', 'integer', 'min:1', 'max:365'],

            // Special filters
            'today'       => ['nullable', 'boolean'],
            'this_week'   => ['nullable', 'boolean'],
            'this_month'  => ['nullable', 'boolean'],
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
            'code',
            'name',
            'city',
            'prefecture',
            'country',
            'is_active',
            'created_at',
            'updated_at',
        ];
    }
}