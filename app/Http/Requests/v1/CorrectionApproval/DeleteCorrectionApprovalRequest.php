<?php

namespace App\Http\Requests\v1\CorrectionApproval;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCorrectionApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('correction_approval'));
    }

    public function rules(): array
    {
        return [];
    }
}