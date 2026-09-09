<?php

namespace App\Http\Requests\v1\ApplicantDocument;

use App\Models\ApplicantDocument;
use Illuminate\Foundation\Http\FormRequest;

class GetAllApplicantDocumentRequest extends FormRequest
{
    private const DEFAULT_ORDER_BY  = 'created_at';
    private const DEFAULT_ORDER_DIR = 'desc';
    private const DEFAULT_LIMIT     = 10;
    private const MAX_LIMIT         = 100;

    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', ApplicantDocument::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        // Fallback sanitization for sorting & pagination
        $this->merge([
            'order_by'  => $this->getValidOrderBy(),
            'order_dir' => $this->getValidOrderDir(),
            'limit'     => $this->getValidLimit(),
        ]);
    }

    public function rules(): array
    {
        return [
            'search'               => ['nullable', 'string', 'min:1', 'max:100'],
            'offset'               => ['nullable', 'integer', 'min:0'],
            'limit'                => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_LIMIT],
            'order_by'             => ['nullable', 'string'],
            'order_dir'            => ['nullable', 'in:asc,desc'],

            // Filters
            'applicant_id'         => ['nullable', 'integer', 'exists:applicants,id'],
            'document_type_id'     => ['nullable', 'integer', 'exists:document_types,id'],
            'status'               => ['nullable', 'in:uploaded,pending_verification,under_review,verified,rejected,expired,requires_correction'],
            'priority'             => ['nullable', 'in:low,normal,high,urgent'],
            'is_expired'           => ['nullable', 'boolean'],
            'uploaded_by'          => ['nullable', 'integer', 'exists:users,id'],
            'is_current_version'   => ['nullable', 'boolean'],

            // Special filters
            'current_version_only' => ['nullable', 'boolean'],
            'expiring_soon'        => ['nullable', 'boolean'],
            'expiring_within_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'urgent_only'          => ['nullable', 'boolean'],
        ];
    }

    protected function getValidOrderBy(): string
    {
        $orderBy = $this->input('order_by');

        return is_string($orderBy) && in_array($orderBy, $this->getValidColumns(), true)
            ? $orderBy
            : self::DEFAULT_ORDER_BY;
    }

    protected function getValidOrderDir(): string
    {
        $orderDir = strtolower((string) $this->input('order_dir'));

        return in_array($orderDir, ['asc', 'desc'], true)
            ? $orderDir
            : self::DEFAULT_ORDER_DIR;
    }

    protected function getValidLimit(): int
    {
        $limit = $this->input('limit');

        if (!is_numeric($limit)) {
            return self::DEFAULT_LIMIT;
        }

        return max(1, min(self::MAX_LIMIT, (int) $limit));
    }

    protected function getValidColumns(): array
    {
        return [
            'id',
            'status',
            'priority',
            'expiry_date',
            'document_date',
            'version',
            'created_at',
            'updated_at',
        ];
    }
}