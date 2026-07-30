<?php

namespace App\Http\Requests\v1\CorrectionApproval;

use App\Models\CorrectionApproval;
use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectionApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', CorrectionApproval::class);
    }

    public function rules(): array
    {
        return [
            'correction_request_id' => ['required', 'integer', 'exists:correction_requests,id'],
            'approval_level'        => ['nullable', 'integer', 'min:1', 'max:5'],
            'decision'              => ['nullable', 'in:pending,approved,rejected,escalated'],
            'comments'              => ['nullable', 'string', 'max:2000'],
            'conditions'            => ['nullable', 'array'],
        ];
    }
}