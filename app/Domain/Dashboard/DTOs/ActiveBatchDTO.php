<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final class ActiveBatchDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $batchNumber,
        public readonly int $applicantsCount,
        public readonly int $verifiedCount,
        public readonly int $targetCount,
        public readonly string $status,
        public readonly ?string $deploymentDate = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'batch_number'     => $this->batchNumber,
            'applicants_count' => $this->applicantsCount,
            'verified_count'   => $this->verifiedCount,
            'target_count'     => $this->targetCount,
            'status'           => $this->status,
            'deployment_date'  => $this->deploymentDate,
        ];
    }
}