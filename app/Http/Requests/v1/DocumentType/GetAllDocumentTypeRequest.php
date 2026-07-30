<?php

namespace App\Http\Requests\v1\DocumentType;

use App\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;

class GetAllDocumentTypeRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'sort_order';
    private const DEFAULT_ORDER_DIR = 'asc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', DocumentType::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'order_by'      => $this->getValidOrderBy(),
            'order_dir'     => $this->getValidOrderDir(),
            'limit'         => $this->getValidLimit(),
            'is_active'     => $this->toBool($this->input('is_active')),
            'is_required'   => $this->toBool($this->input('is_required')),
            'active_only'   => $this->toBool($this->input('active_only')),
            'required_only' => $this->toBool($this->input('required_only')),
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
            'is_active'     => ['nullable', 'boolean'],
            'is_required'   => ['nullable', 'boolean'],
            'category'      => ['nullable', 'in:primary,supporting'],
            'active_only'   => ['nullable', 'boolean'],
            'required_only' => ['nullable', 'boolean'],
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
            'name',
            'code',
            'category',
            'sort_order',
            'validity_days',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Convert "true"/"false"/"1"/"0"/1/0/true/false → real bool (or null).
     * Prevents Laravel's `boolean` rule from choking on stringified booleans
     * that some HTTP clients (like axios) send by default in query strings.
     */
    private function toBool($value): ?bool
    {
        if ($value === null || $value === '') return null;
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}