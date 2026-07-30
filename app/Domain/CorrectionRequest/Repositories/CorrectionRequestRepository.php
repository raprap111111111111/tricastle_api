<?php

namespace App\Domain\CorrectionRequest\Repositories;

use App\Models\CorrectionRequest;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class CorrectionRequestRepository extends BaseRepository
{
    protected string $model = CorrectionRequest::class;

    protected array $relations = [
        'documentVerification',
        'applicantDocument',
        'requester',
    ];

    protected array $searchable = [
        'request_code',
        'description',
        'justification',
    ];

    protected array $filterable = [
        'status',
        'severity',
        'requested_by',
        'document_verification_id',
        'applicant_document_id',
        'requires_approval',
        'requires_new_document',
    ];

    protected array $sortable = [
        'id',
        'request_code',
        'status',
        'severity',
        'due_date',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->boolean('overdue_only')) {
            $query->overdue();
        }

        if ($request->boolean('due_soon')) {
            $days = (int) $request->input('due_within_days', 3);
            $query->dueSoon($days);
        }

        if ($request->boolean('critical_only')) {
            $query->critical();
        }

        if ($request->boolean('requires_approval_only')) {
            $query->requiresApproval();
        }

        if ($request->boolean('active_only')) {
            $query->whereIn('status', ['pending', 'under_review', 'approved']);
        }

        return $query;
    }
}