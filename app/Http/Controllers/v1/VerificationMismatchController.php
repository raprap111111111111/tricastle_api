<?php

namespace App\Http\Controllers\v1;

use App\Domain\VerificationMismatch\Actions\CreateVerificationMismatchAction;
use App\Domain\VerificationMismatch\Actions\DeleteVerificationMismatchAction;
use App\Domain\VerificationMismatch\Actions\EscalateVerificationMismatchAction;
use App\Domain\VerificationMismatch\Actions\GetVerificationMismatchAction;
use App\Domain\VerificationMismatch\Actions\ListVerificationMismatchesAction;
use App\Domain\VerificationMismatch\Actions\ResolveVerificationMismatchAction;
use App\Domain\VerificationMismatch\Actions\UpdateVerificationMismatchAction;
use App\Domain\VerificationMismatch\Actions\WaiveVerificationMismatchAction;
use App\Domain\VerificationMismatch\Mappers\VerificationMismatchMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\VerificationMismatch\DeleteVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\EscalateVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\GetAllVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\GetVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\ResolveVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\StoreVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\UpdateVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\WaiveVerificationMismatchRequest;
use App\Http\Resources\v1\VerificationMismatchResource;
use App\Models\VerificationMismatch;
use Illuminate\Http\JsonResponse;

class VerificationMismatchController extends Controller
{
    public function __construct(
        private readonly ListVerificationMismatchesAction  $listAction,
        private readonly GetVerificationMismatchAction     $getAction,
        private readonly CreateVerificationMismatchAction  $createAction,
        private readonly UpdateVerificationMismatchAction  $updateAction,
        private readonly DeleteVerificationMismatchAction  $deleteAction,
        private readonly ResolveVerificationMismatchAction $resolveAction,
        private readonly WaiveVerificationMismatchAction   $waiveAction,
        private readonly EscalateVerificationMismatchAction $escalateAction,
    ) {}

    public function index(GetAllVerificationMismatchRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            VerificationMismatchResource::class
        );

        return $this->responseSuccess($result, 'Verification mismatches retrieved successfully');
    }

    public function show(GetVerificationMismatchRequest $request, VerificationMismatch $verificationMismatch): JsonResponse
    {
        $result = $this->getAction->execute($verificationMismatch->id);

        return $this->responseSuccess(
            new VerificationMismatchResource($result),
            'Verification mismatch retrieved successfully'
        );
    }

    public function store(StoreVerificationMismatchRequest $request): JsonResponse
    {
        $mismatch = $this->createAction->execute(
            VerificationMismatchMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new VerificationMismatchResource($mismatch),
            'Verification mismatch created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(UpdateVerificationMismatchRequest $request, VerificationMismatch $verificationMismatch): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $verificationMismatch,
            VerificationMismatchMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new VerificationMismatchResource($updated),
            'Verification mismatch updated successfully'
        );
    }

    public function destroy(DeleteVerificationMismatchRequest $request, VerificationMismatch $verificationMismatch): JsonResponse
    {
        $this->deleteAction->execute($verificationMismatch);

        return $this->responseSuccess(null, 'Verification mismatch deleted successfully');
    }

    public function resolve(ResolveVerificationMismatchRequest $request, VerificationMismatch $verificationMismatch): JsonResponse
    {
        $resolved = $this->resolveAction->execute(
            $verificationMismatch,
            VerificationMismatchMapper::fromResolveRequest($request)
        );

        return $this->responseSuccess(
            new VerificationMismatchResource($resolved),
            'Verification mismatch resolved successfully'
        );
    }

    public function waive(WaiveVerificationMismatchRequest $request, VerificationMismatch $verificationMismatch): JsonResponse
    {
        $waived = $this->waiveAction->execute(
            $verificationMismatch,
            VerificationMismatchMapper::fromWaiveRequest($request)
        );

        return $this->responseSuccess(
            new VerificationMismatchResource($waived),
            'Verification mismatch waived successfully'
        );
    }

    public function escalate(EscalateVerificationMismatchRequest $request, VerificationMismatch $verificationMismatch): JsonResponse
    {
        $escalated = $this->escalateAction->execute(
            $verificationMismatch,
            VerificationMismatchMapper::fromEscalateRequest($request)
        );

        return $this->responseSuccess(
            new VerificationMismatchResource($escalated),
            'Verification mismatch escalated successfully'
        );
    }
}