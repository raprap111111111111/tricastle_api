<?php

namespace App\Domain\ActivityLog\Repositories;

use App\Models\ActivityLog;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogRepository extends BaseRepository
{
    protected string $model = ActivityLog::class;

    protected array $relations = ['user'];

    protected array $searchable = [
        'action',
        'module',
        'description',
        'subject_type',
        'ip_address',
    ];

    protected array $filterable = [
        'user_id',
        'action',
        'module',
        'subject_type',
        'method',
    ];

    protected array $sortable = [
        'id',
        'action',
        'module',
        'subject_type',
        'created_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->boolean('today')) {
            $query->today();
        }

        if ($request->boolean('this_week')) {
            $query->thisWeek();
        }

        if ($request->boolean('this_month')) {
            $query->thisMonth();
        }

        if ($request->filled('recent_days')) {
            $query->recent((int) $request->input('recent_days'));
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        return $query;
    }

    public function log(array $data): ActivityLog
    {
        return ActivityLog::create($data);
    }
}