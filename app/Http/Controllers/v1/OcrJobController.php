<?php

namespace App\Http\Controllers\v1;

use App\Domain\OcrJob\Actions\CancelOcrJobAction;
use App\Domain\OcrJob\Actions\CreateOcrJobAction;
use App\Domain\OcrJob\Actions\DeleteOcrJobAction;
use App\Domain\OcrJob\Actions\GetOcrJobAction;
use App\Domain\OcrJob\Actions\ListOcrJobsAction;
use App\Domain\OcrJob\Actions\QueueOcrJobAction;
use App\Domain\OcrJob\Actions\RetryOcrJobAction;
use App\Domain\OcrJob\Actions\ReviewOcrJobAction;
use App\Domain\OcrJob\Actions\UpdateOcrJobAction;
use App\Domain\OcrJob\Mappers\OcrJobMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\OcrJob\CancelOcrJobRequest;
use App\Http\Requests\v1\OcrJob\GetAllOcrJobRequest;
use App\Http\Requests\v1\OcrJob\QueueOcrJobRequest;
use App\Http\Requests\v1\OcrJob\RetryOcrJobRequest;
use App\Http\Requests\v1\OcrJob\ReviewOcrJobRequest;
use App\Http\Requests\v1\OcrJob\StoreOcrJobRequest;
use App\Http\Requests\v1\OcrJob\UpdateOcrJobRequest;
use App\Http\Resources\v1\OcrJobResource;
use App\Models\OcrJob;
use Illuminate\Http\JsonResponse;

class OcrJobController extends Controller
{
    public function __construct(
        private readonly ListOcrJobsAction   $listAction,
        private readonly GetOcrJobAction     $getAction,
        private readonly CreateOcrJobAction  $createAction,
        private readonly UpdateOcrJobAction  $updateAction,
        private readonly DeleteOcrJobAction  $deleteAction,
        private readonly QueueOcrJobAction   $queueAction,
        private readonly CancelOcrJobAction  $cancelAction,
        private readonly RetryOcrJobAction   $retryAction,
        private readonly ReviewOcrJobAction  $reviewAction,
    ) {}

    public function index(GetAllOcrJobRequest $request): JsonResponse
    {
        $result = $this->listAction->execute($request->validated(), OcrJobResource::class);
        return $this->responseSuccess($result, 'OCR jobs retrieved successfully');
    }

    public function show(OcrJob $ocrJob): JsonResponse
    {
        return $this->responseSuccess(
            new OcrJobResource($this->getAction->execute($ocrJob->id)),
            'OCR job retrieved successfully'
        );
    }

    public function store(StoreOcrJobRequest $request): JsonResponse
    {
        $result = $this->createAction->execute(OcrJobMapper::fromCreateRequest($request));
        return $this->responseSuccess(new OcrJobResource($result), 'OCR job created successfully', 201);
    }

    public function update(UpdateOcrJobRequest $request, OcrJob $ocrJob): JsonResponse
    {
        $result = $this->updateAction->execute($ocrJob, OcrJobMapper::fromUpdateRequest($request));
        return $this->responseSuccess(new OcrJobResource($result), 'OCR job updated successfully');
    }

    public function destroy(OcrJob $ocrJob): JsonResponse
    {
        $this->deleteAction->execute($ocrJob);
        return $this->responseSuccess(null, 'OCR job deleted successfully');
    }

    public function queue(QueueOcrJobRequest $request, OcrJob $ocrJob): JsonResponse
    {
        $result = $this->queueAction->execute($ocrJob, OcrJobMapper::fromQueueRequest($request, $ocrJob->id));
        return $this->responseSuccess(new OcrJobResource($result), 'OCR job queued successfully');
    }

    public function cancel(CancelOcrJobRequest $request, OcrJob $ocrJob): JsonResponse
    {
        $result = $this->cancelAction->execute($ocrJob, OcrJobMapper::fromCancelRequest($request));
        return $this->responseSuccess(new OcrJobResource($result), 'OCR job cancelled successfully');
    }

    public function retry(RetryOcrJobRequest $request, OcrJob $ocrJob): JsonResponse
    {
        $result = $this->retryAction->execute($ocrJob, OcrJobMapper::fromRetryRequest($request));
        return $this->responseSuccess(new OcrJobResource($result), 'OCR job queued for retry successfully');
    }

    public function review(ReviewOcrJobRequest $request, OcrJob $ocrJob): JsonResponse
    {
        $result = $this->reviewAction->execute($ocrJob, OcrJobMapper::fromReviewRequest($request));
        return $this->responseSuccess(new OcrJobResource($result), 'OCR job reviewed successfully');
    }
}