<?php

namespace App\Http\Controllers\v1;

use App\Domain\BatchGroup\Actions\AssignApplicantsBatchGroupAction;
use App\Domain\BatchGroup\Actions\CreateBatchGroupAction;
use App\Domain\BatchGroup\Actions\DeleteBatchGroupAction;
use App\Domain\BatchGroup\Actions\UpdateBatchGroupAction;
use App\Domain\BatchGroup\Mappers\BatchGroupMapper;
use App\Domain\BatchGroup\Repositories\BatchGroupRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\BatchGroup\AssignApplicantsBatchGroupRequest;
use App\Http\Requests\v1\BatchGroup\GetAllBatchGroupRequest;
use App\Http\Requests\v1\BatchGroup\StoreBatchGroupRequest;
use App\Http\Requests\v1\BatchGroup\UpdateBatchGroupRequest;
use App\Http\Resources\v1\BatchGroupResource;
use App\Models\Batch;
use App\Models\BatchGroup;
use Illuminate\Http\JsonResponse;

class BatchGroupController extends Controller
{
    public function __construct(
        private readonly BatchGroupRepository              $repository,
        private readonly CreateBatchGroupAction           $createAction,
        private readonly UpdateBatchGroupAction           $updateAction,
        private readonly DeleteBatchGroupAction           $deleteAction,
        private readonly AssignApplicantsBatchGroupAction $assignAction,
    ) {}

    public function index(GetAllBatchGroupRequest $request, Batch $batch): JsonResponse
    {
        $groups = $this->repository->getGroupsByBatch($batch->id);

        return $this->responseSuccess(
            BatchGroupResource::collection($groups),
            'Batch groups retrieved successfully'
        );
    }

    public function store(StoreBatchGroupRequest $request, Batch $batch): JsonResponse
    {
        $dto   = BatchGroupMapper::fromStoreRequest($request, $batch->id);
        $group = $this->createAction->execute($dto);

        return (new BatchGroupResource($group))
            ->additional(['message' => 'Batch group created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Batch $batch, BatchGroup $group): JsonResponse
    {
        return (new BatchGroupResource($group))
            ->response()
            ->setStatusCode(200);
    }

    public function update(UpdateBatchGroupRequest $request, Batch $batch, BatchGroup $group): JsonResponse
    {
        $dto     = BatchGroupMapper::fromUpdateRequest($request);
        $updated = $this->updateAction->execute($group, $dto);

        return (new BatchGroupResource($updated))
            ->additional(['message' => 'Batch group updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Batch $batch, BatchGroup $group): JsonResponse
    {
        $this->deleteAction->execute($group);

        return response()->json([
            'message' => 'Batch group deleted successfully.',
        ]);
    }

    public function assignApplicants(AssignApplicantsBatchGroupRequest $request, Batch $batch, BatchGroup $group): JsonResponse
    {
        $dto   = BatchGroupMapper::fromAssignRequest($request, $batch->id, $group->id);
        $count = $this->assignAction->execute($dto);

        return response()->json([
            'success' => true,
            'message' => "{$count} applicants assigned to {$group->name} successfully.",
        ]);
    }
}
