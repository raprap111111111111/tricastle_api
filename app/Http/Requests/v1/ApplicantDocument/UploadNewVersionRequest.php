<?php
// app/Http/Requests/v1/ApplicantDocument/UploadNewVersionRequest.php

namespace App\Http\Requests\v1\ApplicantDocument;

use Illuminate\Foundation\Http\FormRequest;

class UploadNewVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'          => ['required', 'file', 'max:20480'],
            'document_date' => ['nullable', 'date'],
            'expiry_date'   => ['nullable', 'date', 'after:document_date'],
            'notes'         => ['nullable', 'string', 'max:1000'],
            'priority'      => ['nullable', 'string', 'in:low,normal,high,urgent'],
        ];
    }
}