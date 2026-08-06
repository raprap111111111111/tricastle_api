<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Contracts;

use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, array{current: int, previous: int}>
     */
    public function getStatsCounts(array $filters = []): array;

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getRecentActivity(int $limit = 10): Collection;

    /**
     * Trends over last N days (applicants + documents per day).
     *
     * @return array{labels: array<int, string>, applicants: array<int, int>, documents: array<int, int>}
     */
    public function getTrends(int $days = 14): array;

    /**
     * Applicant status breakdown counts.
     *
     * @return array{pending: int, under_review: int, verified: int, rejected: int, incomplete: int}
     */
    public function getStatusBreakdown(): array;

    /**
     * Full recruitment pipeline funnel.
     *
     * @return array{applied: int, documents_submitted: int, under_review: int, verified: int, batched: int, deployed: int}
     */
    public function getPipeline(): array;

    /**
     * Active/preparing batches with progress metrics.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getActiveBatches(int $limit = 5): Collection;

    /**
     * Quick performance stats.
     *
     * @return array{this_month: int, success_rate: float, avg_processing_days: int, active_batches: int}
     */
    public function getQuickStats(): array;

    /**
     * Items needing attention.
     *
     * @return array{expiring_documents: int, pending_corrections: int, verification_mismatches: int, incomplete_applications: int}
     */
    public function getAttention(): array;
}