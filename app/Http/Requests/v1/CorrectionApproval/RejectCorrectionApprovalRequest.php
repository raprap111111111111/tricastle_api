<?php

namespace App\Http\Requests\v1\CorrectionApproval;

use Illuminate\Foundation\Http\FormRequest;

class RejectCorrectionApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reject', $this->route('correction_approval'));
    }

    public function rules(): array
    {
        return [
            'comments' => ['required', 'string', 'max:2000'],
        ];
    }
}