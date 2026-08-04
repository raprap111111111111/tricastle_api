<?php

namespace App\Http\Requests\v1\ActivityLog;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Activitylog\Models\Activity;

class GetAllActivityLogRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 15;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Activity::class);
    }

    protected function prepareForValidation(): void
    {
        // Map frontend-friendly names → Spatie column names
        $this->merge([
            'order_by'  => $this->getValidOrderBy(),
            'order_dir' => $this->getValidOrderDir(),
            'limit'     => $this->getValidLimit(),

            // 🔑 Map user-friendly filter names to Spatie's internal names
            'causer_id'    => $this->input('user_id'),
            'event'        => $this->input('action'),
            'log_name'     => $this->input('module'),
        ]);
    }

    public function rules(): array
    {
        return [
            'search'       => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'       => ['nullable', 'integer', 'min:0'],
            'limit'        => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'     => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'    => ['nullable', 'in:asc,desc'],

            // ─── Original filter names (for API compatibility) ────
            'user_id'      => ['nullable', 'integer', 'exists:users,id'],
            'action'       => ['nullable', 'string', 'max:100'],
            'module'       => ['nullable', 'string', 'max:100'],
            'subject_type' => ['nullable', 'string', 'max:255'],
            'subject_id'   => ['nullable', 'integer'],
            'method'       => ['nullable', 'in:GET,POST,PUT,PATCH,DELETE'],

            // ─── Mapped Spatie names (internal use) ───────────────
            'causer_id'    => ['nullable', 'integer', 'exists:users,id'],
            'event'        => ['nullable', 'string', 'max:100'],
            'log_name'     => ['nullable', 'string', 'max:100'],

            // ─── Date filters ─────────────────────────────────────
            'date_from'    => ['nullable', 'date'],
            'date_to'      => ['nullable', 'date', 'after_or_equal:date_from'],
            'recent_days'  => ['nullable', 'integer', 'min:1', 'max:365'],

            // ─── Special filters ──────────────────────────────────
            'today'        => ['nullable', 'boolean'],
            'this_week'    => ['nullable', 'boolean'],
            'this_month'   => ['nullable', 'boolean'],
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
        // Spatie's actual columns
        return [
            'id',
            'event',
            'log_name',
            'subject_type',
            'causer_id',
            'created_at',
        ];
    }
}