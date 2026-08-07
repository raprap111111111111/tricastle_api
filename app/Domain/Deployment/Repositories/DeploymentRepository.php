<?php

namespace App\Domain\Deployment\Repositories;

use App\Enums\ApplicantBatchStatus;
use App\Models\ApplicantBatch;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DeploymentRepository extends BaseRepository
{
    protected string $model = ApplicantBatch::class;

    protected array $relations = [
        'applicant',
        'batch',
        'processedBy',
        'cancelledBy',
    ];

    protected array $searchable = [
        'deployment_country',
        'deployment_company',
        'deployment_position',
    ];

    protected array $filterable = [
        'status',
        'deployment_country',
        'deployment_company',
        'batch_id',
    ];

    protected array $sortable = [
        'id',
        'deployed_at',
        'deployment_country',
        'deployment_company',
        'contract_start_date',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'deployed_at';
    protected string $defaultOrderDirection = 'desc';

    // ═══════════════════════════════════════════════════════
    // Base Query — Only DEPLOYED records
    // ═══════════════════════════════════════════════════════
    public function query(): Builder
    {
        $query = parent::query();
        $request = request();

        // ─── Only show deployed unless explicitly overridden ───
        if (! $request->filled('include_all_statuses')) {
            $query->where('status', ApplicantBatchStatus::DEPLOYED->value);
        }

        // ─── Search across applicant fields ──────────────
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->whereHas('applicant', function (Builder $q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('applicant_code', 'like', "%{$search}%")
                  ->orWhere('passport_number', 'like', "%{$search}%");
            });
        }

        // ─── Date range: deployed_at ──────────────────────
        if ($request->filled('date_from')) {
            $query->whereDate('deployed_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('deployed_at', '<=', $request->input('date_to'));
        }

        // ─── Country filter ───────────────────────────────
        if ($request->filled('country')) {
            $query->where('deployment_country', $request->input('country'));
        }

        // ─── Company filter (partial) ─────────────────────
        if ($request->filled('company')) {
            $company = trim($request->input('company'));
            $query->where('deployment_company', 'like', "%{$company}%");
        }

        return $query;
    }

    // ═══════════════════════════════════════════════════════
    // Distinct countries (for filter dropdown)
    // ═══════════════════════════════════════════════════════
    public function distinctCountries(): array
    {
        return ApplicantBatch::query()
            ->where('status', ApplicantBatchStatus::DEPLOYED->value)
            ->whereNotNull('deployment_country')
            ->distinct()
            ->orderBy('deployment_country')
            ->pluck('deployment_country')
            ->toArray();
    }

    // ═══════════════════════════════════════════════════════
    // Stats aggregation
    // ═══════════════════════════════════════════════════════
    public function stats(): array
    {
        $baseQuery = ApplicantBatch::query()->where('status', ApplicantBatchStatus::DEPLOYED->value);

        return [
            'total_deployed' => (clone $baseQuery)->count(),

            'today' => (clone $baseQuery)
                ->whereDate('deployed_at', now()->toDateString())
                ->count(),

            'this_week' => (clone $baseQuery)
                ->whereBetween('deployed_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])->count(),

            'this_month' => (clone $baseQuery)
                ->whereMonth('deployed_at', now()->month)
                ->whereYear('deployed_at', now()->year)
                ->count(),

            'by_country' => (clone $baseQuery)
                ->whereNotNull('deployment_country')
                ->selectRaw('deployment_country, COUNT(*) as count')
                ->groupBy('deployment_country')
                ->orderByDesc('count')
                ->pluck('count', 'deployment_country')
                ->toArray(),
        ];
    }
}