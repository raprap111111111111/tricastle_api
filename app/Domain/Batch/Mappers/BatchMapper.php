<?php

namespace App\Domain\Batch\Mappers;

use App\Domain\Batch\DTOs\CreateBatchDTO;
use App\Domain\Batch\DTOs\UpdateBatchDTO;
use App\Http\Requests\v1\Batch\StoreBatchRequest;
use App\Http\Requests\v1\Batch\UpdateBatchRequest;

class BatchMapper
{
    public static function fromStoreRequest(StoreBatchRequest $request): CreateBatchDTO
    {
        return new CreateBatchDTO(
            batchNumber:    (int) $request->validated('batch_number'),
            name:           $request->validated('name'),
            country:        $request->validated('country'),
            deploymentDate: $request->validated('deployment_date'),
            status:         $request->validated('status', 'draft'),
            isActive:       (bool) $request->validated('is_active', false),
            description:    $request->validated('description'),
        );
    }

    public static function fromUpdateRequest(UpdateBatchRequest $request): UpdateBatchDTO
    {
        return new UpdateBatchDTO(
            batchNumber:    $request->validated('batch_number') !== null
                                ? (int) $request->validated('batch_number')
                                : null,
            name:           $request->validated('name'),
            country:        $request->validated('country'),
            deploymentDate: $request->validated('deployment_date'),
            status:         $request->validated('status'),
            isActive:       $request->has('is_active')
                                ? (bool) $request->validated('is_active')
                                : null,
            description:    $request->validated('description'),
        );
    }
}