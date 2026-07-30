<?php

namespace App\Http\Requests\v1\Batch;

use App\Enums\BatchStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'update',
            $this->route('batch')
        );
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:' . implode(',', BatchStatus::values())],
        ];
    }
}