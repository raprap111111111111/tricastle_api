<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\DTOs;

final class DashboardStatsDTO
{
    public function __construct(
        public readonly StatCardDTO $totalApplicants,
        public readonly StatCardDTO $pendingDocuments,
        public readonly StatCardDTO $verifiedToday,
        public readonly StatCardDTO $corrections,
    ) {}

    /** @return array<int, StatCardDTO> */
    public function toArray(): array
    {
        return [
            $this->totalApplicants,
            $this->pendingDocuments,
            $this->verifiedToday,
            $this->corrections,
        ];
    }
}