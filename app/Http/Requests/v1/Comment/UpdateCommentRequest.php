<?php

// app/Http/Requests/v1/Comment/UpdateCommentRequest.php

namespace App\Http\Requests\v1\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('comment'));
    }

    public function rules(): array
    {
        return [
            'content'     => ['nullable', 'string', 'min:1', 'max:5000'],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }
}