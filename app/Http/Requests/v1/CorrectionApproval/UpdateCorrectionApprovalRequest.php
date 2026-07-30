<?php

namespace App\Http\Requests\v1\CorrectionApproval;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCorrectionApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('correction_approval'));
    }

    public function rules(): array
    {
        return [
            'comments'       => ['nullable', 'string', 'max:2000'],
            'conditions'     => ['nullable', 'array'],
            'approval_level' => ['nullable', 'integer', 'min:1', 'max:5'],
        ];
    }
}