<?php
// app/Http/Requests/v1/DocumentExpiryAlert/StoreDocumentExpiryAlertRequest.php

namespace App\Http\Requests\v1\DocumentExpiryAlert;

use App\Models\DocumentExpiryAlert;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentExpiryAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DocumentExpiryAlert::class);
    }

    public function rules(): array
    {
        return [
            'applicant_document_id' => ['required', 'integer', 'exists:applicant_documents,id'],
            'applicant_id'          => ['required', 'integer', 'exists:applicants,id'],
            'days_until_expiry'     => ['required', 'integer'],
            'alert_type'            => ['required', 'in:30_days,60_days,90_days,expired'],
            'expiry_date'           => ['required', 'date'],
        ];
    }
}