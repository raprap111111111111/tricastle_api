<?php

namespace App\Domain\DocumentVersion\Repositories;

use App\Models\DocumentVersion;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DocumentVersionRepository extends BaseRepository
{
    protected string $model = DocumentVersion::class;

    protected array $relations = [
        'applicantDocument',
        'uploader',
    ];

    protected array $searchable = [
        'file_name',
        'change_reason',
    ];

    protected array $filterable = [
        'applicant_document_id',
        'uploaded_by',
        'is_current',
        'mime_type',
    ];

    protected array $sortable = [
        'id',
        'version_number',
        'file_size',
        'is_current',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'version_number';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->boolean('current_only')) {
            $query->current();
        }

        if ($request->boolean('history_only')) {
            $query->notCurrent();
        }

        if ($request->filled('applicant_document_id')) {
            $query->forDocument((int) $request->input('applicant_document_id'));
        }

        return $query;
    }

    public function getNextVersionNumber(int $applicantDocumentId): int
    {
        $max = DocumentVersion::where('applicant_document_id', $applicantDocumentId)
            ->max('version_number');

        return ($max ?? 0) + 1;
    }

    public function unsetCurrentForDocument(int $applicantDocumentId): void
    {
        DocumentVersion::where('applicant_document_id', $applicantDocumentId)
            ->where('is_current', true)
            ->update(['is_current' => false]);
    }
}