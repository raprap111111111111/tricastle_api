<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final class QuickStatsDTO
{
    public function __construct(
        public readonly int $thisMonth = 0,
        public readonly float $successRate = 0.0,
        public readonly int $avgProcessingDays = 0,
        public readonly int $activeBatches = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'this_month'          => $this->thisMonth,
            'success_rate'        => $this->successRate,
            'avg_processing_days' => $this->avgProcessingDays,
            'active_batches'      => $this->activeBatches,
        ];
    }
}