<?php

namespace App\Http\Requests\v1\Batch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('batch'));
    }

    public function rules(): array
    {
        $batchId = $this->route('batch')?->id;

        return [
            'batch_number'    => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('batches', 'batch_number')->ignore($batchId),
            ],
            'name'            => ['nullable', 'string', 'max:255'],
            'country'         => ['nullable', 'string', 'max:100'],
            'deployment_date' => ['nullable', 'date'],
            'status'          => ['nullable', 'in:draft,ongoing,deployed,completed,cancelled'],
            'is_active'       => ['nullable', 'boolean'],
            'description'     => ['nullable', 'string', 'max:2000'],
        ];
    }
}