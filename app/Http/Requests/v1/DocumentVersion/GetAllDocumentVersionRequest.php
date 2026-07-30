<?php

namespace App\Http\Requests\v1\DocumentVersion;

use App\Models\DocumentVersion;
use Illuminate\Foundation\Http\FormRequest;

class GetAllDocumentVersionRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'version_number';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', DocumentVersion::class);
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
            'search'                => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'                => ['nullable', 'integer', 'min:0'],
            'limit'                 => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'              => ['nullable', 'in:' . implode(',', $this->getValidColumns())],
            'order_dir'             => ['nullable', 'in:asc,desc'],

            // Filters
            'applicant_document_id' => ['nullable', 'integer', 'exists:applicant_documents,id'],
            'uploaded_by'           => ['nullable', 'integer', 'exists:users,id'],
            'is_current'            => ['nullable', 'boolean'],
            'mime_type'             => ['nullable', 'string'],

            // Special filters
            'current_only'          => ['nullable', 'boolean'],
            'history_only'          => ['nullable', 'boolean'],
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
        return in_array(strtolower($this->input('order_dir')), ['asc', 'desc'])
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
            'version_number',
            'file_size',
            'is_current',
            'created_at',
            'updated_at',
        ];
    }
}