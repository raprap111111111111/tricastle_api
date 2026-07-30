<?php

namespace App\Http\Requests\v1\CorrectionApproval;

use Illuminate\Foundation\Http\FormRequest;

class ApproveCorrectionApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('approve', $this->route('correction_approval'));
    }

    public function rules(): array
    {
        return [
            'comments'   => ['nullable', 'string', 'max:2000'],
            'conditions' => ['nullable', 'array'],
        ];
    }
}