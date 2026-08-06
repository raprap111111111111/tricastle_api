<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final class TrendDataDTO
{
    /**
     * @param array<int, string> $labels
     * @param array<int, int> $applicants
     * @param array<int, int> $documents
     */
    public function __construct(
        public readonly array $labels,
        public readonly array $applicants,
        public readonly array $documents,
    ) {}

    public function toArray(): array
    {
        return [
            'labels'     => $this->labels,
            'applicants' => $this->applicants,
            'documents'  => $this->documents,
        ];
    }
}