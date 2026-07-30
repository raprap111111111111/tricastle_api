<?php

// app/Http/Requests/v1/Comment/StoreCommentRequest.php

namespace App\Http\Requests\v1\Comment;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Allowed commentable model types.
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
        return $this->user()->can('create', Comment::class);
    }

    public function rules(): array
    {
        return [
            'commentable_type' => ['required', 'string', 'in:' . implode(',', self::COMMENTABLE_TYPES)],
            'commentable_id'   => ['required', 'integer', 'min:1'],
            'content'          => ['required', 'string', 'min:1', 'max:5000'],
            'parent_id'        => ['nullable', 'integer', 'exists:comments,id'],
            'is_internal'      => ['nullable', 'boolean'],
        ];
    }
}