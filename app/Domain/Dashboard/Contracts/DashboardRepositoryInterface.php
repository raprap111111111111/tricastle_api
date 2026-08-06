<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Contracts;

use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    /**
     * Get raw counts for each metric (current + previous period).
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, array{current: int, previous: int}>
     */
    public function getStatsCounts(array $filters = []): array;

    /**
     * Get the latest system-wide activity items.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getRecentActivity(int $limit = 10): Collection;
}