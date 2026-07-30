<?php

namespace App\Http\Requests\v1\FileRepository;

use Illuminate\Foundation\Http\FormRequest;

class DeleteFileRepositoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('file_repository'));
    }

    public function rules(): array
    {
        return [];
    }
}