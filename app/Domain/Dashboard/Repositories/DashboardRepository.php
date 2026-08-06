<?php

declare(strict_types=1);

namespace App\Repositories\Dashboard;

use App\Domains\Dashboard\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, array{current: int, previous: int}>
     */
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

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getRecentActivity(int $limit = 10): Collection
    {
        // If spatie/laravel-activitylog is installed, prefer that
        if (class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            return \Spatie\Activitylog\Models\Activity::query()
                ->latest()
                ->limit($limit)
                ->get()
                ->map(fn ($a) => [
                    'id'          => (string) $a->id,
                    'type'        => (string) ($a->log_name ?? 'info'),
                    'title'       => (string) $a->description,
                    'description' => (string) ($a->properties['description'] ?? ''),
                    'actor'       => $a->causer?->name,
                    'icon'        => $this->iconFor((string) ($a->log_name ?? 'info')),
                    'created_at'  => $a->created_at?->toIso8601String(),
                ]);
        }

        // Fallback: pull latest applicants
        if (!Schema::hasTable('applicants')) {
            return collect();
        }

        return DB::table('applicants')
            ->latest('created_at')
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'created_at'])
            ->map(fn ($row) => [
                'id'          => "applicant-{$row->id}",
                'type'        => 'applicant',
                'title'       => 'New applicant registered',
                'description' => trim("{$row->first_name} {$row->last_name}"),
                'actor'       => null,
                'icon'        => 'pi pi-user-plus',
                'created_at'  => (string) $row->created_at,
            ]);
    }

    /** @return array{current: int, previous: int} */
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

    /** @return array{current: int, previous: int} */
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

    /** @return array{current: int, previous: int} */
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

    /** @return array{current: int, previous: int} */
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
}