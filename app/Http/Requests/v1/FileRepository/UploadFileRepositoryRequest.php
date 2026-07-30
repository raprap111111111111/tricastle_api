<?php

namespace App\Http\Requests\v1\FileRepository;

use App\Models\FileRepository;
use Illuminate\Foundation\Http\FormRequest;

class UploadFileRepositoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', FileRepository::class);
    }

    public function rules(): array
    {
        return [
            'file'           => ['required', 'file', 'max:51200'], // 50MB max
            'disk'           => ['nullable', 'string', 'in:local,s3,public'],
            'storage_driver' => ['nullable', 'string', 'in:local,s3,public'],
            'is_encrypted'   => ['nullable', 'boolean'],
            'metadata'       => ['nullable', 'array'],
        ];
    }
}