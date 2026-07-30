<?php

// app/Http/Requests/v1/OcrTemplate/GetAllOcrTemplateRequest.php

namespace App\Http\Requests\v1\OcrTemplate;

use App\Models\OcrTemplate;
use Illuminate\Foundation\Http\FormRequest;

class GetAllOcrTemplateRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', OcrTemplate::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'order_by'  => $this->getValidOrderBy(),
            'order_dir' => $this->getValidOrderDir(),
            'limit'     => $this->getValidLimit(),
        ]);
    }

    public function rules(): array
    {
        return [
            'search'            => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'            => ['nullable', 'integer', 'min:0'],
            'limit'             => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'          => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'         => ['nullable', 'in:asc,desc'],

            // Filters
            'document_type_id'  => ['nullable', 'integer', 'exists:document_types,id'],
            'preferred_provider'=> ['nullable', 'string', 'in:aws_textract,google_vision,azure_form_recognizer,tesseract,openai_vision,custom_api'],
            'primary_language'  => ['nullable', 'string', 'max:10'],
            'country_code'      => ['nullable', 'string', 'size:2'],
            'orientation'       => ['nullable', 'string', 'in:portrait,landscape'],
            'is_active'         => ['nullable', 'boolean'],
            'is_default'        => ['nullable', 'boolean'],
            'is_verified'       => ['nullable', 'boolean'],
            'is_public'         => ['nullable', 'boolean'],
            'is_beta'           => ['nullable', 'boolean'],
            'created_by'        => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    protected function getValidOrderBy(): string
    {
        return in_array($this->input('order_by'), $this->getValidColumns())
            ? $this->input('order_by')
            : self::DEFAULT_ORDER_BY;
    }

    protected function getValidOrderDir(): string
    {
        return in_array(strtolower((string) $this->input('order_dir')), ['asc', 'desc'])
            ? strtolower($this->input('order_dir'))
            : self::DEFAULT_ORDER_DIR;
    }

    protected function getValidLimit(): int
    {
        return max(1, min(self::MAX_LIMIT, (int) $this->input('limit', self::DEFAULT_LIMIT)));
    }

    protected function getValidColumns(): array
    {
        return [
            'id',
            'name',
            'code',
            'version',
            'priority',
            'times_used',
            'success_rate',
            'avg_confidence',
            'last_used_at',
            'created_at',
            'updated_at',
        ];
    }
}