<?php

namespace App\Http\Requests\v1\Batch;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'delete',
            $this->route('batch')
        );
    }

    public function rules(): array
    {
        return [];
    }
}