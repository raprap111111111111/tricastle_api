<?php

namespace App\Domain\BatchGroup\Actions;

use App\Domain\BatchGroup\DTOs\AssignApplicantsBatchGroupDTO;
use App\Models\ApplicantBatch;

class AssignApplicantsBatchGroupAction
{
    public function execute(AssignApplicantsBatchGroupDTO $dto): int
    {
        return ApplicantBatch::whereIn('id', $dto->applicantBatchIds)
            ->where('batch_id', $dto->batchId)
            ->update(['batch_group_id' => $dto->batchGroupId]);
    }
}
