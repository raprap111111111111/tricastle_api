<?php
// app/Http/Requests/v1/DocumentExpiryAlert/GetDocumentExpiryAlertRequest.php

namespace App\Http\Requests\v1\DocumentExpiryAlert;

use Illuminate\Foundation\Http\FormRequest;

class GetDocumentExpiryAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('document_expiry_alert'));
    }

    public function rules(): array
    {
        return [];
    }
}