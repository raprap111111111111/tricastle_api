<?php

namespace App\Http\Controllers\v1;

use App\Domain\CorrectionRequest\Actions\ApproveCorrectionRequestAction;
use App\Domain\CorrectionRequest\Actions\CancelCorrectionRequestAction;
use App\Domain\CorrectionRequest\Actions\CompleteCorrectionRequestAction;
use App\Domain\CorrectionRequest\Actions\CreateCorrectionRequestAction;
use App\Domain\CorrectionRequest\Actions\DeleteCorrectionRequestAction;
use App\Domain\CorrectionRequest\Actions\GetCorrectionRequestAction;
use App\Domain\CorrectionRequest\Actions\ListCorrectionRequestsAction;
use App\Domain\CorrectionRequest\Actions\RejectCorrectionRequestAction;
use App\Domain\CorrectionRequest\Actions\UpdateCorrectionRequestAction;
use App\Domain\CorrectionRequest\Mappers\CorrectionRequestMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\CorrectionRequest\ApproveCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\CancelCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\CompleteCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\DeleteCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\GetAllCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\GetCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\RejectCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\StoreCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\UpdateCorrectionRequestRequest;
use App\Http\Resources\v1\CorrectionRequestResource;
use App\Models\CorrectionRequest;
use Illuminate\Http\JsonResponse;

class CorrectionRequestController extends Controller
{
    public function __construct(
        private readonly ListCorrectionRequestsAction  $listAction,
        private readonly GetCorrectionRequestAction    $getAction,
        private readonly CreateCorrectionRequestAction $createAction,
        private readonly UpdateCorrectionRequestAction $updateAction,
        private readonly DeleteCorrectionRequestAction $deleteAction,
        private readonly ApproveCorrectionRequestAction $approveAction,
        private readonly RejectCorrectionRequestAction  $rejectAction,
        private readonly CompleteCorrectionRequestAction $completeAction,
        private readonly CancelCorrectionRequestAction   $cancelAction,
    ) {}

    public function index(GetAllCorrectionRequestRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            CorrectionRequestResource::class
        );

        return $this->responseSuccess($result, 'Correction requests retrieved successfully');
    }

    public function show(GetCorrectionRequestRequest $request, CorrectionRequest $correctionRequest): JsonResponse
    {
        $result = $this->getAction->execute($correctionRequest->id);

        return $this->responseSuccess(
            new CorrectionRequestResource($result),
            'Correction request retrieved successfully'
        );
    }

    public function store(StoreCorrectionRequestRequest $request): JsonResponse
    {
        $correctionRequest = $this->createAction->execute(
            CorrectionRequestMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionRequestResource($correctionRequest),
            'Correction request created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(UpdateCorrectionRequestRequest $request, CorrectionRequest $correctionRequest): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $correctionRequest,
            CorrectionRequestMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionRequestResource($updated),
            'Correction request updated successfully'
        );
    }

    public function destroy(DeleteCorrectionRequestRequest $request, CorrectionRequest $correctionRequest): JsonResponse
    {
        $this->deleteAction->execute($correctionRequest);

        return $this->responseSuccess(null, 'Correction request deleted successfully');
    }

    public function approve(ApproveCorrectionRequestRequest $request, CorrectionRequest $correctionRequest): JsonResponse
    {
        $approved = $this->approveAction->execute(
            $correctionRequest,
            CorrectionRequestMapper::fromApproveRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionRequestResource($approved),
            'Correction request approved successfully'
        );
    }

    public function reject(RejectCorrectionRequestRequest $request, CorrectionRequest $correctionRequest): JsonResponse
    {
        $rejected = $this->rejectAction->execute(
            $correctionRequest,
            CorrectionRequestMapper::fromRejectRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionRequestResource($rejected),
            'Correction request rejected successfully'
        );
    }

    public function complete(CompleteCorrectionRequestRequest $request, CorrectionRequest $correctionRequest): JsonResponse
    {
        $completed = $this->completeAction->execute(
            $correctionRequest,
            CorrectionRequestMapper::fromCompleteRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionRequestResource($completed),
            'Correction request completed successfully'
        );
    }

    public function cancel(CancelCorrectionRequestRequest $request, CorrectionRequest $correctionRequest): JsonResponse
    {
        $cancelled = $this->cancelAction->execute(
            $correctionRequest,
            $request->user()->id,
            $request->validated('reason')
        );

        return $this->responseSuccess(
            new CorrectionRequestResource($cancelled),
            'Correction request cancelled successfully'
        );
    }
}