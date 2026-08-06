<?php

namespace App\Http\Requests\v1\Notification;

use App\Models\Notification;
use Illuminate\Foundation\Http\FormRequest;

class GetAllNotificationRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Notification::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'order_by'  => $this->getValidOrderBy(),
            'order_dir' => $this->getValidOrderDir(),
            'limit'     => $this->getValidLimit(),
            // 🎯 Normalize is_read string → boolean
            'is_read'   => $this->normalizeIsRead(),
        ]);
    }

    public function rules(): array
    {
        return [
            'search'    => ['nullable', 'string', 'min:1', 'max:255'],
            'offset'    => ['nullable', 'integer', 'min:0'],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'  => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir' => ['nullable', 'in:asc,desc'],
            'is_read'   => ['nullable', 'boolean'],
            'module'    => ['nullable', 'string', 'max:50'],   // ← ADD module filter
        ];
    }

    /**
     * Convert 'true'/'false' strings to actual booleans
     */
    protected function normalizeIsRead(): mixed
    {
        $value = $this->input('is_read');

        if ($value === null || $value === '') return null;

        // Convert string representations to boolean
        if (in_array($value, [true, 'true', '1', 1], true))  return true;
        if (in_array($value, [false, 'false', '0', 0], true)) return false;

        return $value; // let validator handle invalid values
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
            'created_at',
            'read_at',
        ];
    }
}