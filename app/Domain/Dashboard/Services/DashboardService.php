<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Services;

use App\Domain\Dashboard\Contracts\DashboardRepositoryInterface;
use App\Domain\Dashboard\DTOs\ActiveBatchDTO;
use App\Domain\Dashboard\DTOs\ActivityItemDTO;
use App\Domain\Dashboard\DTOs\AttentionDTO;
use App\Domain\Dashboard\DTOs\DashboardStatsDTO;
use App\Domain\Dashboard\DTOs\PipelineDTO;
use App\Domain\Dashboard\DTOs\QuickStatsDTO;
use App\Domain\Dashboard\DTOs\StatCardDTO;
use App\Domain\Dashboard\DTOs\StatusBreakdownDTO;
use App\Domain\Dashboard\DTOs\TrendDataDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepositoryInterface $repository,
    ) {}

    /**
     * @param array<string, mixed> $filters
     */
    public function getStats(array $filters = []): DashboardStatsDTO
    {
        $cacheKey = 'dashboard.stats.' . md5(json_encode($filters));

        $counts = Cache::remember(
            $cacheKey,
            now()->addMinutes(2),
            fn() => $this->repository->getStatsCounts($filters),
        );

        return new DashboardStatsDTO(
            totalApplicants: $this->buildCard(
                label: 'Total Applicants',
                icon: 'pi pi-users',
                variant: 'apricot',
                counts: $counts['total_applicants'] ?? [],
            ),
            pendingDocuments: $this->buildCard(
                label: 'Pending Docs',
                icon: 'pi pi-file',
                variant: 'blueberry',
                counts: $counts['pending_documents'] ?? [],
            ),
            verifiedToday: $this->buildCard(
                label: 'Verified Today',
                icon: 'pi pi-verified',
                variant: 'citrus',
                counts: $counts['verified_today'] ?? [],
            ),
            corrections: $this->buildCard(
                label: 'Corrections',
                icon: 'pi pi-pencil',
                variant: 'appleCore',
                counts: $counts['corrections'] ?? [],
            ),
        );
    }

    /**
     * @return Collection<int, ActivityItemDTO>
     */
    public function getRecentActivity(int $limit = 10): Collection
    {
        // Cache as ARRAY (not Collection) to avoid serialization issues
        $items = Cache::remember(
            "dashboard.activity.{$limit}",
            now()->addMinute(),
            fn() => $this->repository->getRecentActivity($limit)->toArray(),
        );

        // Convert back to Collection AFTER retrieval
        return collect($items)->map(fn(array $item) => new ActivityItemDTO(
            id: (string) ($item['id'] ?? ''),
            type: (string) ($item['type'] ?? 'info'),
            title: (string) ($item['title'] ?? ''),
            description: $item['description'] ?? null,
            actor: $item['actor'] ?? null,
            icon: (string) ($item['icon'] ?? 'pi pi-info-circle'),
            createdAt: (string) ($item['created_at'] ?? now()->toIso8601String()),
        ));
    }

    // ─── NEW: Trends ──────────────────────────────────
    public function getTrends(int $days = 14): TrendDataDTO
    {
        $data = Cache::remember(
            "dashboard.trends.{$days}",
            now()->addMinutes(5),
            fn() => $this->repository->getTrends($days),
        );

        return new TrendDataDTO(
            labels: $data['labels'] ?? [],
            applicants: $data['applicants'] ?? [],
            documents: $data['documents'] ?? [],
        );
    }

    // ─── NEW: Status Breakdown ────────────────────────
    public function getStatusBreakdown(): StatusBreakdownDTO
    {
        $data = Cache::remember(
            'dashboard.status_breakdown',
            now()->addMinutes(2),
            fn() => $this->repository->getStatusBreakdown(),
        );

        return new StatusBreakdownDTO(
            pending: (int) ($data['pending'] ?? 0),
            underReview: (int) ($data['under_review'] ?? 0),
            verified: (int) ($data['verified'] ?? 0),
            rejected: (int) ($data['rejected'] ?? 0),
            incomplete: (int) ($data['incomplete'] ?? 0),
        );
    }

    // ─── NEW: Pipeline ────────────────────────────────
    public function getPipeline(): PipelineDTO
    {
        $data = Cache::remember(
            'dashboard.pipeline',
            now()->addMinutes(2),
            fn() => $this->repository->getPipeline(),
        );

        return new PipelineDTO(
            applied: (int) ($data['applied'] ?? 0),
            documentsSubmitted: (int) ($data['documents_submitted'] ?? 0),
            underReview: (int) ($data['under_review'] ?? 0),
            verified: (int) ($data['verified'] ?? 0),
            batched: (int) ($data['batched'] ?? 0),
            deployed: (int) ($data['deployed'] ?? 0),
        );
    }

    // ─── NEW: Active Batches ──────────────────────────
    /**
     * @return Collection<int, ActiveBatchDTO>
     */
    public function getActiveBatches(int $limit = 5): Collection
    {
        $items = Cache::remember(
            "dashboard.active_batches.{$limit}",
            now()->addMinutes(2),
            fn() => $this->repository->getActiveBatches($limit)->all(),
        );

        return collect($items)->map(function ($b) {
            $b = is_array($b) ? $b : (array) $b;
            return new ActiveBatchDTO(
                id: (int) ($b['id'] ?? 0),
                name: (string) ($b['name'] ?? ''),
                batchNumber: (string) ($b['batch_number'] ?? ''),
                applicantsCount: (int) ($b['applicants_count'] ?? 0),
                verifiedCount: (int) ($b['verified_count'] ?? 0),
                targetCount: (int) ($b['target_count'] ?? 0),
                status: (string) ($b['status'] ?? 'preparing'),
                deploymentDate: $b['deployment_date'] ?? null,
            );
        });
    }

    // ─── NEW: Quick Stats ─────────────────────────────
    public function getQuickStats(): QuickStatsDTO
    {
        $data = Cache::remember(
            'dashboard.quick_stats',
            now()->addMinutes(5),
            fn() => $this->repository->getQuickStats(),
        );

        return new QuickStatsDTO(
            thisMonth: (int) ($data['this_month'] ?? 0),
            successRate: (float) ($data['success_rate'] ?? 0),
            avgProcessingDays: (int) ($data['avg_processing_days'] ?? 0),
            activeBatches: (int) ($data['active_batches'] ?? 0),
        );
    }

    // ─── NEW: Attention ───────────────────────────────
    public function getAttention(): AttentionDTO
    {
        $data = Cache::remember(
            'dashboard.attention',
            now()->addMinutes(2),
            fn() => $this->repository->getAttention(),
        );

        return new AttentionDTO(
            expiringDocuments: (int) ($data['expiring_documents'] ?? 0),
            pendingCorrections: (int) ($data['pending_corrections'] ?? 0),
            verificationMismatches: (int) ($data['verification_mismatches'] ?? 0),
            incompleteApplications: (int) ($data['incomplete_applications'] ?? 0),
        );
    }

    /**
     * @param array{current?: int, previous?: int} $counts
     */
    private function buildCard(
        string $label,
        string $icon,
        string $variant,
        array $counts,
    ): StatCardDTO {
        $current = (int) ($counts['current'] ?? 0);
        $previous = (int) ($counts['previous'] ?? 0);

        $trend = 0.0;
        if ($previous > 0) {
            $trend = round((($current - $previous) / $previous) * 100, 1);
        } elseif ($current > 0) {
            $trend = 100.0;
        }

        return StatCardDTO::make(
            label: $label,
            value: $current,
            icon: $icon,
            variant: $variant,
            trend: $trend,
        );
    }
}
