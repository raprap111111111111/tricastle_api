<?php

namespace App\Http\Requests\v1\BatchGroup;

use App\Models\BatchGroup;
use Illuminate\Foundation\Http\FormRequest;

class GetAllBatchGroupRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'asc';
    private const DEFAULT_LIMIT     = 50;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', BatchGroup::class);
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
            'limit'     => ['nullable', 'integer', 'min:1', 'max:100'],
            'order_by'  => ['nullable', 'in:id,name,color,max_slots,created_at'],
            'order_dir' => ['nullable', 'in:asc,desc'],
        ];
    }

    protected function getValidOrderBy(): string
    {
        $columns = ['id', 'name', 'color', 'max_slots', 'created_at'];
        return in_array($this->input('order_by'), $columns)
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
        return max(1, min(100, (int) $this->input('limit', self::DEFAULT_LIMIT)));
    }
}
