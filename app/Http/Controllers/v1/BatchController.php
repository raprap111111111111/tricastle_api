<?php

namespace App\Http\Controllers\v1;

use App\Domain\Batch\Actions\CreateBatchAction;
use App\Domain\Batch\Actions\DeleteBatchAction;
use App\Domain\Batch\Actions\UpdateBatchAction;
use App\Domain\Batch\Actions\UpdateBatchStatusAction;
use App\Domain\Batch\Mappers\BatchMapper;
use App\Domain\Batch\Repositories\BatchRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Batch\DeleteBatchRequest;
use App\Http\Requests\v1\Batch\GetAllBatchRequest;
use App\Http\Requests\v1\Batch\GetBatchRequest;
use App\Http\Requests\v1\Batch\StoreBatchRequest;
use App\Http\Requests\v1\Batch\UpdateBatchRequest;
use App\Http\Requests\v1\Batch\UpdateBatchStatusRequest;
use App\Http\Resources\v1\BatchResource;
use App\Models\Batch;
use Illuminate\Http\JsonResponse;

class BatchController extends Controller
{
    public function __construct(
        private readonly BatchRepository $repository,
        private readonly CreateBatchAction $createAction,
        private readonly UpdateBatchAction $updateAction,
        private readonly DeleteBatchAction $deleteAction,
        private readonly UpdateBatchStatusAction $updateStatusAction,
    ) {}

    public function index(
        GetAllBatchRequest $request
    ): JsonResponse {
        $result = $this->repository->paginate(
            $request->validated(),
            BatchResource::class
        );

        return response()->json($result);
    }

    public function store(
        StoreBatchRequest $request
    ): JsonResponse {
        $dto   = BatchMapper::fromStoreRequest($request);
        $batch = $this->createAction->execute($dto);

        return (new BatchResource($batch))
            ->additional(['message' => 'Batch created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        GetBatchRequest $request,
        Batch $batch
    ): JsonResponse {
        return (new BatchResource($batch))
            ->response()
            ->setStatusCode(200);
    }

    public function update(
        UpdateBatchRequest $request,
        Batch $batch
    ): JsonResponse {
        $dto     = BatchMapper::fromUpdateRequest($request);
        $updated = $this->updateAction->execute($batch, $dto);

        return (new BatchResource($updated))
            ->additional(['message' => 'Batch updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(
        DeleteBatchRequest $request,
        Batch $batch
    ): JsonResponse {
        $this->deleteAction->execute($batch);

        return response()->json([
            'message' => 'Batch deleted successfully.',
        ]);
    }

    public function updateStatus(
        UpdateBatchStatusRequest $request,
        Batch $batch
    ): JsonResponse {
        $dto     = BatchMapper::fromUpdateStatusRequest($request);
        $updated = $this->updateStatusAction->execute($batch, $dto);

        return (new BatchResource($updated))
            ->additional(['message' => 'Batch status updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Get the currently active batch.
     */
    public function active(): JsonResponse
    {
        $batch = $this->repository->getActiveBatch();

        if (!$batch) {
            return $this->responseSuccess(null, 'No active batch');
        }

        return $this->responseSuccess(
            new BatchResource($batch),
            'Active batch retrieved successfully'
        );
    }

    /**
     * Activate a specific batch (deactivates all others).
     */
    public function activate(Batch $batch): JsonResponse
    {
        $activated = $this->repository->activate($batch->id);

        return $this->responseSuccess(
            new BatchResource($activated),
            "Batch #{$activated->batch_number} has been activated"
        );
    }

    /**
     * Deactivate a specific batch.
     */
    public function deactivate(Batch $batch): JsonResponse
    {
        $deactivated = $this->repository->deactivate($batch->id);

        return $this->responseSuccess(
            new BatchResource($deactivated),
            "Batch #{$deactivated->batch_number} has been deactivated"
        );
    }
}
