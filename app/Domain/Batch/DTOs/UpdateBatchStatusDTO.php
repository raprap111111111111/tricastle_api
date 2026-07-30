<?php

// app/Domain/Batch/DTOs/UpdateBatchStatusDTO.php

namespace App\Domain\Batch\DTOs;

final readonly class UpdateBatchStatusDTO
{
    public function __construct(
        public string $status,
    ) {}
}