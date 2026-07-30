<?php

namespace App\Http\Requests\v1\OcrJob;

use App\Models\OcrJob;
use Illuminate\Foundation\Http\FormRequest;

class StoreOcrJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', OcrJob::class);
    }

    public function rules(): array
    {
        return [
            'applicant_document_id' => ['required', 'integer', 'exists:applicant_documents,id'],
            'file_repository_id'    => ['nullable', 'integer', 'exists:file_repository,id'],
            'ocr_template_id'       => ['nullable', 'integer'],
            'batch_id'              => ['nullable', 'string', 'max:255'],
            'provider'              => ['nullable', 'in:aws_textract,google_vision,azure_form_recognizer,tesseract,openai_vision,manual,custom_api'],
            'provider_config'       => ['nullable', 'array'],
            'priority'              => ['nullable', 'integer', 'min:1', 'max:10'],
            'notes'                 => ['nullable', 'string', 'max:1000'],
            'metadata'              => ['nullable', 'array'],
        ];
    }
}