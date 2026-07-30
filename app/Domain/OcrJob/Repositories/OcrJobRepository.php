<?php

namespace App\Domain\OcrJob\Repositories;

use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class OcrJobRepository extends BaseRepository
{
    protected string $model = \App\Models\OcrJob::class;

    protected array $relations = [
        'document',
        'fileRepository',
        'template',
        'initiator',
        'reviewer',
    ];

    protected array $searchable = [
        'job_code',
        'batch_id',
        'external_job_id',
        'detected_document_type',
        'error_message',
    ];

    protected array $filterable = [
        'status',
        'provider',
        'batch_id',
        'detected_document_type',
        'is_document_type_matched',
        'is_blurry',
        'has_glare',
        'is_free_tier',
        'priority',
        'applicant_document_id',
        'initiated_by',
        'reviewed_by',
    ];

    protected array $sortable = [
        'created_at',
        'job_code',
        'status',
        'priority',
        'overall_confidence',
        'processing_time_ms',
        'api_cost',
        'queued_at',
        'started_at',
        'completed_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        // Filter by minimum confidence threshold
        if ($request->filled('min_confidence')) {
            $query->where('overall_confidence', '>=', $request->input('min_confidence'));
        }

        // Filter by maximum confidence threshold
        if ($request->filled('max_confidence')) {
            $query->where('overall_confidence', '<=', $request->input('max_confidence'));
        }

        // Filter by date range: queued_at
        if ($request->filled('queued_from')) {
            $query->whereDate('queued_at', '>=', $request->input('queued_from'));
        }
        if ($request->filled('queued_to')) {
            $query->whereDate('queued_at', '<=', $request->input('queued_to'));
        }

        // Filter by date range: completed_at
        if ($request->filled('completed_from')) {
            $query->whereDate('completed_at', '>=', $request->input('completed_from'));
        }
        if ($request->filled('completed_to')) {
            $query->whereDate('completed_at', '<=', $request->input('completed_to'));
        }

        // Filter by priority range
        if ($request->filled('min_priority')) {
            $query->where('priority', '>=', $request->input('min_priority'));
        }

        // Filter jobs that can be retried
        if ($request->boolean('retryable')) {
            $query->whereIn('status', ['failed', 'timeout'])
                  ->whereColumn('attempt_number', '<', 'max_attempts');
        }

        return $query;
    }
}