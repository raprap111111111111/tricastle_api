<?php

namespace App\Domain\Internship\DTOs;

final readonly class SyncGuarantorsDTO
{
    /** @param array<int, array<string, mixed>> $guarantors */
    public function __construct(
        public int   $applicantId,
        public array $guarantors,
    ) {}
}