<?php

namespace App\Domain\BatchGroup\DTOs;

final readonly class AssignApplicantsBatchGroupDTO
{
    /**
     * @param int[] $applicantBatchIds
     */
    public function __construct(
        public int   $batchId,
        public int   $batchGroupId,
        public array $applicantBatchIds,
    ) {}
}
