<?php

// app/Http/Requests/v1/Comment/GetCommentRequest.php

namespace App\Http\Requests\v1\Comment;

use Illuminate\Foundation\Http\FormRequest;

class GetCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('comment'));
    }

    public function rules(): array
    {
        return [];
    }
}