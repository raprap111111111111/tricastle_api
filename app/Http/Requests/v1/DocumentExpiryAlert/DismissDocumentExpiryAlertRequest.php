<?php
// app/Http/Requests/v1/DocumentExpiryAlert/DismissDocumentExpiryAlertRequest.php

namespace App\Http\Requests\v1\DocumentExpiryAlert;

use Illuminate\Foundation\Http\FormRequest;

class DismissDocumentExpiryAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('dismiss', $this->route('document_expiry_alert'));
    }

    public function rules(): array
    {
        return [];
    }
}