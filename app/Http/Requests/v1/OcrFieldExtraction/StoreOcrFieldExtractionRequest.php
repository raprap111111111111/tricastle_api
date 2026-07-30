<?php

namespace App\Http\Requests\v1\OcrFieldExtraction;

use App\Models\OcrFieldExtraction;
use Illuminate\Foundation\Http\FormRequest;

class StoreOcrFieldExtractionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', OcrFieldExtraction::class);
    }

    public function rules(): array
    {
        return [
            'ocr_job_id'            => ['required', 'integer', 'exists:ocr_jobs,id'],
            'applicant_document_id' => ['required', 'integer', 'exists:applicant_documents,id'],
            'field_name'            => ['required', 'string', 'max:255'],
            'field_label'           => ['required', 'string', 'max:255'],
            'field_type'            => ['required', 'string', 'max:100'],
            'field_category'        => ['nullable', 'string', 'max:100'],
            'is_required'           => ['nullable', 'boolean'],
            'is_primary_field'      => ['nullable', 'boolean'],
            'sort_order'            => ['nullable', 'integer', 'min:0'],
            'extracted_value'       => ['nullable', 'string'],
            'normalized_value'      => ['nullable', 'string'],
            'confidence_score'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'confidence_level'      => ['nullable', 'in:very_high,high,medium,low,very_low,unknown'],
            'character_confidence'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'word_confidence'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'character_count'       => ['nullable', 'integer', 'min:0'],
            'word_count'            => ['nullable', 'integer', 'min:0'],
            'bounding_box'          => ['nullable', 'array'],
            'bounding_box.x'        => ['required_with:bounding_box', 'numeric'],
            'bounding_box.y'        => ['required_with:bounding_box', 'numeric'],
            'bounding_box.width'    => ['required_with:bounding_box', 'numeric'],
            'bounding_box.height'   => ['required_with:bounding_box', 'numeric'],
            'page_number'           => ['nullable', 'integer', 'min:1'],
            'x_coordinate'          => ['nullable', 'numeric'],
            'y_coordinate'          => ['nullable', 'numeric'],
            'width'                 => ['nullable', 'numeric', 'min:0'],
            'height'                => ['nullable', 'numeric', 'min:0'],
            'rotation_angle'        => ['nullable', 'numeric', 'min:-360', 'max:360'],
            'status'                => ['nullable', 'in:extracted,validated,requires_review,manually_corrected,accepted,rejected,missing,skipped,auto_filled'],
            'source'                => ['nullable', 'in:ocr,manual,api'],
            'notes'                 => ['nullable', 'string', 'max:1000'],
            'metadata'              => ['nullable', 'array'],
        ];
    }
}