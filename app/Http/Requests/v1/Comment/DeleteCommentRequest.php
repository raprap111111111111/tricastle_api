<?php

// app/Http/Requests/v1/Comment/DeleteCommentRequest.php

namespace App\Http\Requests\v1\Comment;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('comment'));
    }

    public function rules(): array
    {
        return [];
    }
}