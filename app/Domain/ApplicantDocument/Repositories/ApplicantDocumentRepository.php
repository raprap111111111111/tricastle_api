<?php

namespace App\Domain\ApplicantDocument\Repositories;

use App\Models\ApplicantDocument;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ApplicantDocumentRepository extends BaseRepository
{
    protected string $model = ApplicantDocument::class;

    // 🎯 ADDED 'fileRepository' so file paths and R2 disk metadata eager-load efficiently
    protected array $relations = [
        'applicant',
        'documentType',
        'fileRepository',
        'uploader',
        'verifier',
        'rejector',
    ];

    protected array $searchable = [
        'file_name',
        'file_hash',
        'notes',
    ];

    protected array $filterable = [
        'applicant_id',
        'document_type_id',
        'status',
        'priority',
        'is_expired',
        'is_current_version',
        'uploaded_by',
    ];

    protected array $sortable = [
        'id',
        'status',
        'priority',
        'expiry_date',
        'document_date',
        'version',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        // ──────────────────────────────────────────────────
        // Version & Expiry Filters
        // ──────────────────────────────────────────────────

        if ($request->boolean('current_version_only')) {
            $query->currentVersion();
        }

        if ($request->boolean('expiring_soon')) {
            $days = (int) $request->input('expiring_within_days', 30);
            $query->expiringSoon($days);
        }

        if ($request->boolean('urgent_only')) {
            $query->urgent();
        }

        // ──────────────────────────────────────────────────
        // 🎯 BATCH FILTER (Supports filtering documents by Training Batch)
        // ──────────────────────────────────────────────────
        if ($request->filled('batch_id')) {
            $batchId = (int) $request->input('batch_id');
            $query->whereHas('applicant.applicantBatches', function (Builder $q) use ($batchId) {
                $q->where('batch_id', $batchId);
            });
        }

        return $query;
    }
}