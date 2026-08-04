<?php

namespace App\Domain\ActivityLog\Repositories;

use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ActivityLogRepository extends BaseRepository
{
    protected string $model = Activity::class;

    protected array $relations = ['causer', 'subject'];

    protected array $searchable = [
        'event',
        'log_name',
        'description',
        'subject_type',
    ];

    protected array $filterable = [
        'causer_id',
        'event',
        'log_name',
        'subject_type',
        'subject_id',
    ];

    protected array $sortable = [
        'id',
        'event',
        'log_name',
        'subject_type',
        'causer_id',
        'created_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        // ── Date filters ─────────────────────────────
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // ── Quick date shortcuts ─────────────────────
        if ($request->boolean('today')) {
            $query->whereDate('created_at', now()->toDateString());
        }

        if ($request->boolean('this_week')) {
            $query->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]);
        }

        if ($request->boolean('this_month')) {
            $query->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ]);
        }

        if ($request->filled('recent_days')) {
            $query->where('created_at', '>=', now()->subDays((int) $request->input('recent_days')));
        }

        // ── HTTP Method filter (stored in properties) ─
        if ($request->filled('method')) {
            $query->where('properties->method', $request->input('method'));
        }

        return $query;
    }
}