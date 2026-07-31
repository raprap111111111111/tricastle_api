<?php
// app/Http/Requests/v1/ApplicantDocument/GetExpiringDocumentsRequest.php

namespace App\Http\Requests\v1\ApplicantDocument;

use App\Models\ApplicantDocument;
use Illuminate\Foundation\Http\FormRequest;

class GetExpiringDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', ApplicantDocument::class);
    }

    public function rules(): array
    {
        return [
            'search'           => ['nullable', 'string', 'max:255'],
            'alert_type'       => ['nullable', 'string', 'in:30_days,60_days,90_days,expired'],
            'applicant_id'     => ['nullable', 'integer', 'exists:applicants,id'],
            'document_type_id' => ['nullable', 'integer', 'exists:document_types,id'],
            'order_by'         => ['nullable', 'in:expiry_date,created_at'],
            'order_dir'        => ['nullable', 'in:asc,desc'],
            'limit'            => ['nullable', 'integer', 'min:1', 'max:100'],
            'offset'           => ['nullable', 'integer', 'min:0'],
        ];
    }
}