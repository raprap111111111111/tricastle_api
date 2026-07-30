<?php

// app/Http/Requests/v1/Comment/GetAllCommentRequest.php

namespace App\Http\Requests\v1\Comment;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class GetAllCommentRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    /**
     * Allowed commentable model types.
     * Add your morphable models here.
     */
    private const COMMENTABLE_TYPES = [
        'App\\Models\\Applicant',
        'App\\Models\\Document',
        'App\\Models\\CorrectionRequest',
        'App\\Models\\OcrJob',
        'App\\Models\\OcrTemplate',
    ];

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Comment::class);
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
            'search'           => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'           => ['nullable', 'integer', 'min:0'],
            'limit'            => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'         => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'        => ['nullable', 'in:asc,desc'],

            // Filters
            'user_id'          => ['nullable', 'integer', 'exists:users,id'],
            'commentable_type' => ['nullable', 'string', 'in:' . implode(',', self::COMMENTABLE_TYPES)],
            'commentable_id'   => ['nullable', 'integer', 'min:1'],
            'parent_id'        => ['nullable', 'integer', 'exists:comments,id'],
            'is_internal'      => ['nullable', 'boolean'],
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
            'user_id',
            'commentable_type',
            'commentable_id',
            'is_internal',
            'created_at',
            'updated_at',
        ];
    }
}