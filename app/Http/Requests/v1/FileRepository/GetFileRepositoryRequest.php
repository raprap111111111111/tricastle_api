<?php

namespace App\Http\Requests\v1\FileRepository;

use Illuminate\Foundation\Http\FormRequest;

class GetFileRepositoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('file_repository'));
    }

    public function rules(): array
    {
        return [];
    }
}