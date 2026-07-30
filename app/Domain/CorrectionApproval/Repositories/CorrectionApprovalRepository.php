<?php

namespace App\Domain\CorrectionApproval\Repositories;

use App\Models\CorrectionApproval;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class CorrectionApprovalRepository extends BaseRepository
{
    protected string $model = CorrectionApproval::class;

    protected array $relations = [
        'correctionRequest',
        'approver',
    ];

    protected array $searchable = [
        'comments',
    ];

    protected array $filterable = [
        'correction_request_id',
        'approver_id',
        'decision',
        'approval_level',
    ];

    protected array $sortable = [
        'id',
        'decision',
        'approval_level',
        'decided_at',
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

        if ($request->boolean('decided_only')) {
            $query->whereNotNull('decided_at');
        }

        if ($request->filled('level')) {
            $query->byLevel((int) $request->input('level'));
        }

        if ($request->boolean('supervisor_level')) {
            $query->supervisorLevel();
        }

        if ($request->boolean('admin_level')) {
            $query->adminLevel();
        }

        return $query;
    }
}