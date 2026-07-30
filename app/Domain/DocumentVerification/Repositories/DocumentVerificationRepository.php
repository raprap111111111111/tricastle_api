<?php

namespace App\Domain\DocumentVerification\Repositories;

use App\Models\DocumentVerification;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DocumentVerificationRepository extends BaseRepository
{
    protected string $model = DocumentVerification::class;

    protected array $relations = [
        'applicantDocument',
        'verifier',
        'reviewer',
        'mismatches',
    ];

    protected array $searchable = [
        'notes',
        'rejection_reason',
    ];

    protected array $filterable = [
        'status',
        'verified_by',
        'reviewed_by',
        'applicant_document_id',
    ];

    protected array $sortable = [
        'id',
        'status',
        'match_percentage',
        'total_fields',
        'matched_fields',
        'mismatched_fields',
        'missing_fields',
        'started_at',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->boolean('pending_only')) {
            $query->pending();
        }

        if ($request->boolean('in_progress_only')) {
            $query->inProgress();
        }

        if ($request->filled('min_match_percentage')) {
            $query->where('match_percentage', '>=', (float) $request->input('min_match_percentage'));
        }

        if ($request->filled('max_match_percentage')) {
            $query->where('match_percentage', '<=', (float) $request->input('max_match_percentage'));
        }

        return $query;
    }
}