<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Contracts\DashboardRepositoryInterface;
use App\Domains\Dashboard\DTOs\ActivityItemDTO;
use App\Domains\Dashboard\DTOs\DashboardStatsDTO;
use App\Domains\Dashboard\DTOs\StatCardDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepositoryInterface $repository,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getStats(array $filters = []): DashboardStatsDTO
    {
        $cacheKey = 'dashboard.stats.' . md5(json_encode($filters));

        /** @var array<string, array{current: int, previous: int}> $counts */
        $counts = Cache::remember(
            $cacheKey,
            now()->addMinutes(2),
            fn () => $this->repository->getStatsCounts($filters),
        );

        return new DashboardStatsDTO(
            totalApplicants: $this->buildCard(
                label:   'Total Applicants',
                icon:    'pi pi-users',
                variant: 'apricot',
                counts:  $counts['total_applicants'] ?? [],
            ),
            pendingDocuments: $this->buildCard(
                label:   'Pending Docs',
                icon:    'pi pi-file',
                variant: 'blueberry',
                counts:  $counts['pending_documents'] ?? [],
            ),
            verifiedToday: $this->buildCard(
                label:   'Verified Today',
                icon:    'pi pi-verified',
                variant: 'citrus',
                counts:  $counts['verified_today'] ?? [],
            ),
            corrections: $this->buildCard(
                label:   'Corrections',
                icon:    'pi pi-pencil',
                variant: 'appleCore',
                counts:  $counts['corrections'] ?? [],
            ),
        );
    }

    /**
     * @return Collection<int, ActivityItemDTO>
     */
    public function getRecentActivity(int $limit = 10): Collection
    {
        $items = Cache::remember(
            "dashboard.activity.{$limit}",
            now()->addMinute(),
            fn () => $this->repository->getRecentActivity($limit),
        );

        return $items->map(fn (array $item) => new ActivityItemDTO(
            id:          (string) ($item['id'] ?? ''),
            type:        (string) ($item['type'] ?? 'info'),
            title:       (string) ($item['title'] ?? ''),
            description: $item['description'] ?? null,
            actor:       $item['actor'] ?? null,
            icon:        (string) ($item['icon'] ?? 'pi pi-info-circle'),
            createdAt:   (string) ($item['created_at'] ?? now()->toIso8601String()),
        ));
    }

    /**
     * Build a StatCardDTO with computed trend percentage.
     *
     * @param  array{current?: int, previous?: int}  $counts
     */
    private function buildCard(
        string $label,
        string $icon,
        string $variant,
        array $counts,
    ): StatCardDTO {
        $current  = (int) ($counts['current']  ?? 0);
        $previous = (int) ($counts['previous'] ?? 0);

        $trend = 0.0;
        if ($previous > 0) {
            $trend = round((($current - $previous) / $previous) * 100, 1);
        } elseif ($current > 0) {
            $trend = 100.0;
        }

        return StatCardDTO::make(
            label:   $label,
            value:   $current,
            icon:    $icon,
            variant: $variant,
            trend:   $trend,
        );
    }
}