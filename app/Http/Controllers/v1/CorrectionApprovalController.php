<?php

namespace App\Http\Controllers\v1;

use App\Domain\CorrectionApproval\Actions\ApproveCorrectionApprovalAction;
use App\Domain\CorrectionApproval\Actions\CreateCorrectionApprovalAction;
use App\Domain\CorrectionApproval\Actions\DeleteCorrectionApprovalAction;
use App\Domain\CorrectionApproval\Actions\EscalateCorrectionApprovalAction;
use App\Domain\CorrectionApproval\Actions\GetCorrectionApprovalAction;
use App\Domain\CorrectionApproval\Actions\ListCorrectionApprovalsAction;
use App\Domain\CorrectionApproval\Actions\RejectCorrectionApprovalAction;
use App\Domain\CorrectionApproval\Actions\UpdateCorrectionApprovalAction;
use App\Domain\CorrectionApproval\Mappers\CorrectionApprovalMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\CorrectionApproval\ApproveCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\DeleteCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\EscalateCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\GetAllCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\GetCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\RejectCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\StoreCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\UpdateCorrectionApprovalRequest;
use App\Http\Resources\v1\CorrectionApprovalResource;
use App\Models\CorrectionApproval;
use Illuminate\Http\JsonResponse;

class CorrectionApprovalController extends Controller
{
    public function __construct(
        private readonly ListCorrectionApprovalsAction   $listAction,
        private readonly GetCorrectionApprovalAction     $getAction,
        private readonly CreateCorrectionApprovalAction  $createAction,
        private readonly UpdateCorrectionApprovalAction  $updateAction,
        private readonly DeleteCorrectionApprovalAction  $deleteAction,
        private readonly ApproveCorrectionApprovalAction $approveAction,
        private readonly RejectCorrectionApprovalAction  $rejectAction,
        private readonly EscalateCorrectionApprovalAction $escalateAction,
    ) {}

    public function index(GetAllCorrectionApprovalRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            CorrectionApprovalResource::class
        );

        return $this->responseSuccess($result, 'Correction approvals retrieved successfully');
    }

    public function show(GetCorrectionApprovalRequest $request, CorrectionApproval $correctionApproval): JsonResponse
    {
        $result = $this->getAction->execute($correctionApproval->id);

        return $this->responseSuccess(
            new CorrectionApprovalResource($result),
            'Correction approval retrieved successfully'
        );
    }

    public function store(StoreCorrectionApprovalRequest $request): JsonResponse
    {
        $approval = $this->createAction->execute(
            CorrectionApprovalMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionApprovalResource($approval),
            'Correction approval created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(UpdateCorrectionApprovalRequest $request, CorrectionApproval $correctionApproval): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $correctionApproval,
            CorrectionApprovalMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionApprovalResource($updated),
            'Correction approval updated successfully'
        );
    }

    public function destroy(DeleteCorrectionApprovalRequest $request, CorrectionApproval $correctionApproval): JsonResponse
    {
        $this->deleteAction->execute($correctionApproval);

        return $this->responseSuccess(null, 'Correction approval deleted successfully');
    }

    public function approve(ApproveCorrectionApprovalRequest $request, CorrectionApproval $correctionApproval): JsonResponse
    {
        $approved = $this->approveAction->execute(
            $correctionApproval,
            CorrectionApprovalMapper::fromApproveRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionApprovalResource($approved),
            'Correction approval approved successfully'
        );
    }

    public function reject(RejectCorrectionApprovalRequest $request, CorrectionApproval $correctionApproval): JsonResponse
    {
        $rejected = $this->rejectAction->execute(
            $correctionApproval,
            CorrectionApprovalMapper::fromRejectRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionApprovalResource($rejected),
            'Correction approval rejected successfully'
        );
    }

    public function escalate(EscalateCorrectionApprovalRequest $request, CorrectionApproval $correctionApproval): JsonResponse
    {
        $escalated = $this->escalateAction->execute(
            $correctionApproval,
            CorrectionApprovalMapper::fromEscalateRequest($request)
        );

        return $this->responseSuccess(
            new CorrectionApprovalResource($escalated),
            'Correction approval escalated successfully'
        );
    }
}