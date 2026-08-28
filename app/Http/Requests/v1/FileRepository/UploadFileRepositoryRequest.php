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
        // 🎯 Dynamically allow all configured disks (local, public, s3, r2, etc.)
        $validDisks = implode(',', array_keys(config('filesystems.disks', [
            'local'  => [],
            'public' => [],
            's3'     => [],
            'r2'     => [],
        ])));

        return [
            'file'           => ['required', 'file', 'max:51200'], // 50MB max
            'disk'           => ['nullable', 'string', 'in:' . $validDisks],
            'storage_driver' => ['nullable', 'string', 'in:' . $validDisks],
            'is_encrypted'   => ['nullable', 'boolean'],
            'metadata'       => ['nullable', 'array'],
        ];
    }
}