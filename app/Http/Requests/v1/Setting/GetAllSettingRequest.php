<?php

// app/Http/Requests/v1/Setting/GetAllSettingRequest.php

namespace App\Http\Requests\v1\Setting;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class GetAllSettingRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'group';
    private const DEFAULT_ORDER_DIR = 'asc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Setting::class);
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
            'order_by'  => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir' => ['nullable', 'in:asc,desc'],

            // Filters
            'group'     => ['nullable', 'string', 'max:100'],
            'type'      => ['nullable', 'string', 'in:string,integer,boolean,json'],
            'is_public' => ['nullable', 'boolean'],
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
            'key',
            'group',
            'type',
            'is_public',
            'created_at',
            'updated_at',
        ];
    }
}