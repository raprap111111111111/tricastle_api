<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardRepository implements DashboardRepositoryInterface
{
    // ─── Existing: Stats Counts ───────────────────────
    public function getStatsCounts(array $filters = []): array
    {
        $now         = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $lastWeek    = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();
        $today       = $now->copy()->startOfDay();
        $yesterday   = $now->copy()->subDay()->startOfDay();

        return [
            'total_applicants'  => $this->totalApplicants($startOfWeek),
            'pending_documents' => $this->pendingDocuments($startOfWeek),
            'verified_today'    => $this->verifiedToday($today, $yesterday, $now),
            'corrections'       => $this->corrections($lastWeek, $lastWeekEnd),
        ];
    }

    // ─── Existing: Recent Activity ────────────────────
    public function getRecentActivity(int $limit = 10): Collection
    {
        if (class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            return \Spatie\Activitylog\Models\Activity::query()
                ->latest()
                ->limit($limit)
                ->get()
                ->map(fn($a) => [
                    'id'          => (string) $a->id,
                    'type'        => (string) ($a->log_name ?? 'info'),
                    'title'       => (string) $a->description,
                    'description' => (string) ($a->properties['description'] ?? ''),
                    'actor'       => $a->causer?->name,
                    'icon'        => $this->iconFor((string) ($a->log_name ?? 'info')),
                    'created_at'  => $a->created_at?->toIso8601String(),
                ])
                ->values();
        }

        if (!Schema::hasTable('applicants')) {
            return collect();
        }

        return DB::table('applicants')
            ->latest('created_at')
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'created_at'])
            ->map(fn($row) => [
                'id'          => "applicant-{$row->id}",
                'type'        => 'applicant',
                'title'       => 'New applicant registered',
                'description' => trim("{$row->first_name} {$row->last_name}"),
                'actor'       => null,
                'icon'        => 'pi pi-user-plus',
                'created_at'  => (string) $row->created_at,
            ])
            ->values();
    }

    // ─── NEW: Trends ──────────────────────────────────
    public function getTrends(int $days = 14): array
    {
        $labels = [];
        $applicants = [];
        $documents = [];

        $hasApplicants = Schema::hasTable('applicants');
        $hasDocuments = Schema::hasTable('documents');

        $applicantsByDate = $hasApplicants
            ? DB::table('applicants')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays($days - 1)->startOfDay())
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray()
            : [];

        $documentsByDate = $hasDocuments
            ? DB::table('documents')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays($days - 1)->startOfDay())
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray()
            : [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateKey = $date->format('Y-m-d');

            $labels[] = $date->format('M j');
            $applicants[] = (int) ($applicantsByDate[$dateKey] ?? 0);
            $documents[] = (int) ($documentsByDate[$dateKey] ?? 0);
        }

        return [
            'labels' => $labels,
            'applicants' => $applicants,
            'documents' => $documents,
        ];
    }

    // ─── NEW: Status Breakdown ────────────────────────
    public function getStatusBreakdown(): array
    {
        if (!Schema::hasTable('applicants')) {
            return [
                'pending' => 0,
                'under_review' => 0,
                'verified' => 0,
                'rejected' => 0,
                'incomplete' => 0,
            ];
        }

        $counts = DB::table('applicants')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'pending'      => (int) ($counts['pending'] ?? 0),
            'under_review' => (int) ($counts['under_review'] ?? 0),
            'verified'     => (int) ($counts['verified'] ?? 0),
            'rejected'     => (int) ($counts['rejected'] ?? 0),
            'incomplete'   => (int) ($counts['incomplete'] ?? 0),
        ];
    }

    // ─── NEW: Pipeline ────────────────────────────────
    public function getPipeline(): array
    {
        $applied = Schema::hasTable('applicants')
            ? DB::table('applicants')->count()
            : 0;

        $documentsSubmitted = Schema::hasTable('documents')
            ? DB::table('documents')
            ->distinct('applicant_id')
            ->count('applicant_id')
            : 0;

        $underReview = Schema::hasTable('applicants')
            ? DB::table('applicants')
            ->where('status', 'under_review')
            ->count()
            : 0;

        $verified = Schema::hasTable('applicants')
            ? DB::table('applicants')
            ->where('status', 'verified')
            ->count()
            : 0;

        $batched = Schema::hasTable('applicant_batches')
            ? DB::table('applicant_batches')
            ->distinct('applicant_id')
            ->count('applicant_id')
            : 0;

        $deployed = 0;
        if (Schema::hasTable('batches') && Schema::hasTable('applicant_batches')) {
            $deployed = DB::table('batches')
                ->join('applicant_batches', 'batches.id', '=', 'applicant_batches.batch_id')
                ->where('batches.status', 'deployed')
                ->distinct('applicant_batches.applicant_id')
                ->count('applicant_batches.applicant_id');
        }

        return [
            'applied'             => $applied,
            'documents_submitted' => $documentsSubmitted,
            'under_review'        => $underReview,
            'verified'            => $verified,
            'batched'             => $batched,
            'deployed'            => $deployed,
        ];
    }

    // ─── NEW: Active Batches ──────────────────────────
    public function getActiveBatches(int $limit = 5): Collection
    {
        if (!Schema::hasTable('batches')) {
            return collect();
        }

        $hasApplicantBatches = Schema::hasTable('applicant_batches');
        $hasApplicants = Schema::hasTable('applicants');

        // Detect what column to order by
        $orderColumn = Schema::hasColumn('batches', 'deployment_date')
            ? 'deployment_date'
            : (Schema::hasColumn('batches', 'created_at') ? 'created_at' : 'id');

        // Detect status values available - use YOUR actual values
        $activeStatuses = ['draft', 'preparing', 'in_progress', 'ready', 'active'];

        $rows = DB::table('batches')
            ->whereIn('status', $activeStatuses)
            ->orderBy($orderColumn, 'asc')
            ->limit($limit)
            ->get();

        // Build a plain array-of-arrays
        $items = [];
        foreach ($rows as $b) {
            $applicantsCount = 0;
            $verifiedCount = 0;

            if ($hasApplicantBatches) {
                $applicantsCount = DB::table('applicant_batches')
                    ->where('batch_id', $b->id)
                    ->count();

                if ($hasApplicants) {
                    $verifiedCount = DB::table('applicant_batches')
                        ->join('applicants', 'applicant_batches.applicant_id', '=', 'applicants.id')
                        ->where('applicant_batches.batch_id', $b->id)
                        ->where('applicants.status', 'verified')
                        ->count();
                }
            }

            // Safe fallbacks for missing/optional columns
            $targetCount = (int) (
                $b->target_count
                ?? $b->capacity
                ?? $b->max_applicants
                ?? 20  // default if no column exists
            );

            $items[] = [
                'id'               => (int) $b->id,
                'name'             => (string) ($b->name ?? "Batch #{$b->id}"),
                'batch_number'     => (string) ($b->batch_number ?? "B-{$b->id}"),
                'applicants_count' => $applicantsCount,
                'verified_count'   => $verifiedCount,
                'target_count'     => $targetCount,
                'status'           => (string) ($b->status ?? 'draft'),
                'deployment_date'  => $b->deployment_date ?? null,
            ];
        }

        return collect($items);
    }
    // ─── NEW: Quick Stats ─────────────────────────────
    public function getQuickStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        $thisMonth = Schema::hasTable('applicants')
            ? DB::table('applicants')
            ->where('created_at', '>=', $startOfMonth)
            ->count()
            : 0;

        $successRate = 0.0;
        if (Schema::hasTable('applicants')) {
            $verified = DB::table('applicants')->where('status', 'verified')->count();
            $rejected = DB::table('applicants')->where('status', 'rejected')->count();
            $total = $verified + $rejected;
            if ($total > 0) {
                $successRate = round(($verified / $total) * 100, 1);
            }
        }

        $avgProcessingDays = 0;
        if (Schema::hasTable('applicants')) {
            $driver = DB::connection()->getDriverName();

            if (in_array($driver, ['mysql', 'mariadb'])) {
                $avg = DB::table('applicants')
                    ->whereIn('status', ['verified', 'rejected'])
                    ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                    ->value('avg_days');
            } elseif ($driver === 'pgsql') {
                $avg = DB::table('applicants')
                    ->whereIn('status', ['verified', 'rejected'])
                    ->selectRaw('AVG(EXTRACT(DAY FROM (updated_at - created_at))) as avg_days')
                    ->value('avg_days');
            } else {
                $avg = DB::table('applicants')
                    ->whereIn('status', ['verified', 'rejected'])
                    ->selectRaw("AVG(julianday(updated_at) - julianday(created_at)) as avg_days")
                    ->value('avg_days');
            }

            $avgProcessingDays = (int) round((float) ($avg ?? 0));
        }

        $activeBatches = Schema::hasTable('batches')
            ? DB::table('batches')
            ->whereIn('status', ['preparing', 'in_progress', 'ready'])
            ->count()
            : 0;

        return [
            'this_month'          => $thisMonth,
            'success_rate'        => $successRate,
            'avg_processing_days' => $avgProcessingDays,
            'active_batches'      => $activeBatches,
        ];
    }

    // ─── NEW: Attention ───────────────────────────────
    public function getAttention(): array
    {
        $now = Carbon::now();
        $in30Days = $now->copy()->addDays(30);

        // Documents expiring in next 30 days
        $expiring = 0;
        if (Schema::hasTable('documents') && Schema::hasColumn('documents', 'expiry_date')) {
            $expiring = DB::table('documents')
                ->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$now, $in30Days])
                ->count();
        }

        // Pending correction requests
        $corrections = 0;
        if (Schema::hasTable('correction_requests') && Schema::hasColumn('correction_requests', 'status')) {
            $corrections = DB::table('correction_requests')
                ->whereIn('status', ['pending', 'in_review'])
                ->count();
        }

        // Verification mismatches - defensive column check
        $mismatches = 0;
        if (Schema::hasTable('verification_mismatches')) {
            $query = DB::table('verification_mismatches');

            if (Schema::hasColumn('verification_mismatches', 'resolved')) {
                $query->where('resolved', false);
            } elseif (Schema::hasColumn('verification_mismatches', 'resolved_at')) {
                $query->whereNull('resolved_at');
            } elseif (Schema::hasColumn('verification_mismatches', 'status')) {
                $query->whereIn('status', ['pending', 'unresolved', 'open']);
            }

            $mismatches = $query->count();
        }

        // Incomplete applicants
        $incomplete = 0;
        if (Schema::hasTable('applicants') && Schema::hasColumn('applicants', 'status')) {
            $incomplete = DB::table('applicants')
                ->where('status', 'incomplete')
                ->count();
        }

        return [
            'expiring_documents'      => $expiring,
            'pending_corrections'     => $corrections,
            'verification_mismatches' => $mismatches,
            'incomplete_applications' => $incomplete,
        ];
    }

    // ─── Private helpers ──────────────────────────────
    private function totalApplicants(Carbon $startOfWeek): array
    {
        if (!Schema::hasTable('applicants')) {
            return ['current' => 0, 'previous' => 0];
        }

        return [
            'current'  => DB::table('applicants')->count(),
            'previous' => DB::table('applicants')
                ->where('created_at', '<', $startOfWeek)
                ->count(),
        ];
    }

    private function pendingDocuments(Carbon $startOfWeek): array
    {
        if (!Schema::hasTable('documents')) {
            return ['current' => 0, 'previous' => 0];
        }

        return [
            'current' => DB::table('documents')
                ->where('status', 'pending')
                ->count(),
            'previous' => DB::table('documents')
                ->where('status', 'pending')
                ->where('created_at', '<', $startOfWeek)
                ->count(),
        ];
    }

    private function verifiedToday(Carbon $today, Carbon $yesterday, Carbon $now): array
    {
        if (!Schema::hasTable('documents')) {
            return ['current' => 0, 'previous' => 0];
        }

        return [
            'current' => DB::table('documents')
                ->where('status', 'verified')
                ->whereBetween('updated_at', [$today, $now])
                ->count(),
            'previous' => DB::table('documents')
                ->where('status', 'verified')
                ->whereBetween('updated_at', [$yesterday, $today])
                ->count(),
        ];
    }

    private function corrections(Carbon $lastWeek, Carbon $lastWeekEnd): array
    {
        if (!Schema::hasTable('correction_requests')) {
            return ['current' => 0, 'previous' => 0];
        }

        return [
            'current' => DB::table('correction_requests')
                ->whereIn('status', ['pending', 'in_review'])
                ->count(),
            'previous' => DB::table('correction_requests')
                ->whereIn('status', ['pending', 'in_review'])
                ->whereBetween('created_at', [$lastWeek, $lastWeekEnd])
                ->count(),
        ];
    }

    private function iconFor(string $type): string
    {
        return match (strtolower($type)) {
            'applicant', 'user'    => 'pi pi-user-plus',
            'document'             => 'pi pi-file',
            'approval', 'verified' => 'pi pi-verified',
            'correction'           => 'pi pi-pencil',
            default                => 'pi pi-info-circle',
        };
    }

    // ─── NEW: Birthdays ───────────────────────────────
    public function getBirthdays(): array
    {
        $today = Carbon::today();

        $mapPerson = function (object $row) use ($today): array {
            $dob = Carbon::parse($row->date_of_birth)->startOfDay();

            // Next birthday from today
            $next = $dob->copy()->year($today->year);
            if ($next->lt($today)) {
                $next->addYear();
            }

            $daysLeft = (int) $today->diffInDays($next, false);
            $turningAge = (int) ($next->year - $dob->year);

            return [
                'id'             => (int) $row->id,
                'name'           => trim((string) ($row->name ?? '')),
                'date_of_birth'  => $dob->format('Y-m-d'),
                'age'            => $turningAge,
                'days_left'      => $daysLeft,
                'is_today'       => $daysLeft === 0,
                'formatted_date' => $next->format('M j'),
            ];
        };

        // ── Applicants ──────────────────────────────────
        $applicants = [];
        if (Schema::hasTable('applicants') && Schema::hasColumn('applicants', 'date_of_birth')) {
            if (Schema::hasColumn('applicants', 'first_name')) {
                $last = Schema::hasColumn('applicants', 'last_name')
                    ? ", ' ', COALESCE(last_name,'')"
                    : '';
                $nameExpr = "TRIM(CONCAT(COALESCE(first_name,''){$last}))";
            } elseif (Schema::hasColumn('applicants', 'name')) {
                $nameExpr = 'name';
            } else {
                $nameExpr = "CONCAT('Applicant #', id)";
            }

            $rows = DB::table('applicants')
                ->whereNotNull('date_of_birth')
                ->select([
                    'id',
                    'date_of_birth',
                    DB::raw("{$nameExpr} as name"),
                ])
                ->get();

            $applicants = $rows->map($mapPerson)->values()->all();
        }

        // ── Staff ───────────────────────────────────────
        $staff = [];
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'date_of_birth')) {
            $query = DB::table('users')->whereNotNull('date_of_birth');

            // Optional: only staff-like roles
            if (Schema::hasTable('model_has_roles') && Schema::hasTable('roles')) {
                $query->whereIn('users.id', function ($q) {
                    $q->select('model_id')
                        ->from('model_has_roles')
                        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->where('model_type', 'App\\Models\\User')
                        ->whereIn('roles.name', ['admin', 'staff', 'super_admin', 'manager']);
                });
            }

            // Build name from real columns (users has no plain `name` in your DB)
            if (Schema::hasColumn('users', 'name')) {
                $nameExpr = 'name';
            } elseif (Schema::hasColumn('users', 'first_name')) {
                $last = Schema::hasColumn('users', 'last_name')
                    ? ", ' ', COALESCE(last_name,'')"
                    : '';
                $nameExpr = "TRIM(CONCAT(COALESCE(first_name,''){$last}))";
            } elseif (Schema::hasColumn('users', 'full_name')) {
                $nameExpr = 'full_name';
            } else {
                $nameExpr = "CONCAT('User #', id)";
            }

            $rows = $query->select([
                'id',
                'date_of_birth',
                DB::raw("{$nameExpr} as name"),
            ])->get();

            $staff = $rows->map($mapPerson)->values()->all();
        }

        // Sort by soonest birthday
        usort($applicants, fn($a, $b) => $a['days_left'] <=> $b['days_left']);
        usort($staff, fn($a, $b) => $a['days_left'] <=> $b['days_left']);

        return [
            'applicants' => $applicants,
            'staff'      => $staff,
        ];
    }
}
