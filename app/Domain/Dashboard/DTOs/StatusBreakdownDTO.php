<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final class StatusBreakdownDTO
{
    public function __construct(
        public readonly int $pending = 0,
        public readonly int $underReview = 0,
        public readonly int $verified = 0,
        public readonly int $rejected = 0,
        public readonly int $incomplete = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'pending'      => $this->pending,
            'under_review' => $this->underReview,
            'verified'     => $this->verified,
            'rejected'     => $this->rejected,
            'incomplete'   => $this->incomplete,
        ];
    }
}