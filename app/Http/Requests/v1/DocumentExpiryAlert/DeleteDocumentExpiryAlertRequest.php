<?php
// app/Http/Requests/v1/DocumentExpiryAlert/DeleteDocumentExpiryAlertRequest.php

namespace App\Http\Requests\v1\DocumentExpiryAlert;

use Illuminate\Foundation\Http\FormRequest;

class DeleteDocumentExpiryAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('document_expiry_alert'));
    }

    public function rules(): array
    {
        return [];
    }
}