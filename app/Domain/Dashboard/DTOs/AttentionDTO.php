<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final class AttentionDTO
{
    public function __construct(
        public readonly int $expiringDocuments = 0,
        public readonly int $pendingCorrections = 0,
        public readonly int $verificationMismatches = 0,
        public readonly int $incompleteApplications = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'expiring_documents'      => $this->expiringDocuments,
            'pending_corrections'     => $this->pendingCorrections,
            'verification_mismatches' => $this->verificationMismatches,
            'incomplete_applications' => $this->incompleteApplications,
        ];
    }
}