<?php

namespace App\Domain\BatchGroup\Mappers;

use App\Domain\BatchGroup\DTOs\AssignApplicantsBatchGroupDTO;
use App\Domain\BatchGroup\DTOs\CreateBatchGroupDTO;
use App\Domain\BatchGroup\DTOs\UpdateBatchGroupDTO;
use App\Http\Requests\v1\BatchGroup\AssignApplicantsBatchGroupRequest;
use App\Http\Requests\v1\BatchGroup\StoreBatchGroupRequest;
use App\Http\Requests\v1\BatchGroup\UpdateBatchGroupRequest;

class BatchGroupMapper
{
    public static function fromStoreRequest(StoreBatchGroupRequest $request, int $batchId): CreateBatchGroupDTO
    {
        return new CreateBatchGroupDTO(
            batchId:  $batchId,
            name:     $request->validated('name'),
            color:    $request->validated('color', 'blue'),
            maxSlots: $request->validated('max_slots'),
        );
    }

    public static function fromUpdateRequest(UpdateBatchGroupRequest $request): UpdateBatchGroupDTO
    {
        return new UpdateBatchGroupDTO(
            name:     $request->validated('name'),
            color:    $request->validated('color'),
            maxSlots: $request->validated('max_slots'),
        );
    }

    public static function fromAssignRequest(AssignApplicantsBatchGroupRequest $request, int $batchId, int $groupId): AssignApplicantsBatchGroupDTO
    {
        return new AssignApplicantsBatchGroupDTO(
            batchId:           $batchId,
            batchGroupId:      $groupId,
            applicantBatchIds: $request->validated('applicant_batch_ids'),
        );
    }
}
