<?php

// app/Http/Requests/v1/OcrTemplate/StoreOcrTemplateRequest.php

namespace App\Http\Requests\v1\OcrTemplate;

use App\Models\OcrTemplate;
use Illuminate\Foundation\Http\FormRequest;

class StoreOcrTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', OcrTemplate::class);
    }

    public function rules(): array
    {
        return [
            // Template Info
            'document_type_id'        => ['required', 'integer', 'exists:document_types,id'],
            'name'                    => ['required', 'string', 'max:255'],
            'code'                    => ['required', 'string', 'max:255', 'unique:ocr_templates,code'],
            'version'                 => ['nullable', 'string', 'max:20'],
            'description'             => ['nullable', 'string'],
            'thumbnail_path'          => ['nullable', 'string', 'max:500'],

            // Detection
            'detection_keywords'      => ['nullable', 'array'],
            'detection_patterns'      => ['nullable', 'array'],
            'detection_features'      => ['nullable', 'array'],
            'sample_image_path'       => ['nullable', 'string', 'max:500'],
            'detection_threshold'     => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Specifications
            'expected_width'          => ['nullable', 'integer', 'min:1'],
            'expected_height'         => ['nullable', 'integer', 'min:1'],
            'aspect_ratio'            => ['nullable', 'numeric', 'min:0'],
            'orientation'             => ['nullable', 'string', 'in:portrait,landscape'],
            'paper_size'              => ['nullable', 'string', 'max:50'],
            'expected_pages'          => ['nullable', 'integer', 'min:1'],
            'color_mode'              => ['nullable', 'string', 'in:color,grayscale,bw'],

            // Fields
            'field_definitions'       => ['nullable', 'array'],
            'field_positions'         => ['nullable', 'array'],
            'field_relationships'     => ['nullable', 'array'],
            'validation_rules'        => ['nullable', 'array'],
            'required_fields'         => ['nullable', 'array'],
            'optional_fields'         => ['nullable', 'array'],

            // OCR Provider
            'preferred_provider'      => ['nullable', 'string', 'in:aws_textract,google_vision,azure_form_recognizer,tesseract,openai_vision,custom_api'],
            'provider_settings'       => ['nullable', 'array'],
            'fallback_providers'      => ['nullable', 'array'],
            'confidence_threshold'    => ['nullable', 'integer', 'min:0', 'max:100'],

            // Image Processing
            'requires_preprocessing'  => ['nullable', 'boolean'],
            'preprocessing_steps'     => ['nullable', 'array'],
            'auto_rotate'             => ['nullable', 'boolean'],
            'auto_enhance'            => ['nullable', 'boolean'],
            'auto_deskew'             => ['nullable', 'boolean'],
            'remove_background'       => ['nullable', 'boolean'],
            'binarize'                => ['nullable', 'boolean'],

            // Language & Region
            'primary_language'        => ['nullable', 'string', 'max:10'],
            'supported_languages'     => ['nullable', 'array'],
            'country_code'            => ['nullable', 'string', 'size:2'],
            'region'                  => ['nullable', 'string', 'max:100'],

            // Status & Config
            'is_active'               => ['nullable', 'boolean'],
            'is_default'              => ['nullable', 'boolean'],
            'is_verified'             => ['nullable', 'boolean'],
            'is_public'               => ['nullable', 'boolean'],
            'is_beta'                 => ['nullable', 'boolean'],
            'priority'                => ['nullable', 'integer', 'min:1', 'max:10'],

            // Access Control
            'allowed_roles'           => ['nullable', 'array'],
            'restricted_users'        => ['nullable', 'array'],

            // Meta
            'notes'                   => ['nullable', 'string'],
            'tags'                    => ['nullable', 'array'],
            'metadata'                => ['nullable', 'array'],
            'changelog'               => ['nullable', 'string'],
        ];
    }
}