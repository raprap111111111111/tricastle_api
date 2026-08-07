<?php

namespace App\Domain\Deployment\DTOs;

final readonly class CancelDeploymentDTO
{
    public function __construct(
        public string  $cancellationReason,
        public ?int    $cancelledBy = null,
    ) {}
}