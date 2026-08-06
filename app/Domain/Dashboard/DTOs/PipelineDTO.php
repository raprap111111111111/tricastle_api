<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final class PipelineDTO
{
    public function __construct(
        public readonly int $applied = 0,
        public readonly int $documentsSubmitted = 0,
        public readonly int $underReview = 0,
        public readonly int $verified = 0,
        public readonly int $batched = 0,
        public readonly int $deployed = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'applied'             => $this->applied,
            'documents_submitted' => $this->documentsSubmitted,
            'under_review'        => $this->underReview,
            'verified'            => $this->verified,
            'batched'             => $this->batched,
            'deployed'            => $this->deployed,
        ];
    }
}