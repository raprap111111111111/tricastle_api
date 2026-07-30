<?php

namespace App\Http\Requests\v1\FileRepository;

use App\Models\FileRepository;
use Illuminate\Foundation\Http\FormRequest;

class GetAllFileRepositoryRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', FileRepository::class);
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
            'search'         => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'         => ['nullable', 'integer', 'min:0'],
            'limit'          => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'       => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'      => ['nullable', 'in:asc,desc'],
            'disk'           => ['nullable', 'string', 'in:local,s3,public'],
            'mime_type'      => ['nullable', 'string'],
            'is_encrypted'   => ['nullable', 'boolean'],
            'uploaded_by'    => ['nullable', 'integer', 'exists:users,id'],
            'unused_only'    => ['nullable', 'boolean'],
            'encrypted_only' => ['nullable', 'boolean'],
            'min_size'       => ['nullable', 'integer', 'min:0'],
            'max_size'       => ['nullable', 'integer', 'min:0'],
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
            'original_name',
            'file_size',
            'mime_type',
            'disk',
            'reference_count',
            'created_at',
            'updated_at',
        ];
    }
}