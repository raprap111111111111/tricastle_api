<?php

namespace App\Http\Requests\v1\Batch;

use Illuminate\Foundation\Http\FormRequest;

class GetBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'view',
            $this->route('batch')
        );
    }

    public function rules(): array
    {
        return [];
    }
}