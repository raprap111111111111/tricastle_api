<?php

namespace App\Http\Controllers\v1;

use App\Domain\DocumentVerification\Actions\ApproveDocumentVerificationAction;
use App\Domain\DocumentVerification\Actions\CompleteDocumentVerificationAction;
use App\Domain\DocumentVerification\Actions\CreateDocumentVerificationAction;
use App\Domain\DocumentVerification\Actions\DeleteDocumentVerificationAction;
use App\Domain\DocumentVerification\Actions\GetDocumentVerificationAction;
use App\Domain\DocumentVerification\Actions\ListDocumentVerificationsAction;
use App\Domain\DocumentVerification\Actions\RejectDocumentVerificationAction;
use App\Domain\DocumentVerification\Actions\StartDocumentVerificationAction;
use App\Domain\DocumentVerification\Actions\UpdateDocumentVerificationAction;
use App\Domain\DocumentVerification\Mappers\DocumentVerificationMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\DocumentVerification\ApproveDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\CompleteDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\DeleteDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\GetAllDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\GetDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\RejectDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\StartDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\StoreDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\UpdateDocumentVerificationRequest;
use App\Http\Resources\v1\DocumentVerificationResource;
use App\Models\DocumentVerification;
use Illuminate\Http\JsonResponse;

class DocumentVerificationController extends Controller
{
    public function __construct(
        private readonly ListDocumentVerificationsAction  $listAction,
        private readonly GetDocumentVerificationAction    $getAction,
        private readonly CreateDocumentVerificationAction $createAction,
        private readonly UpdateDocumentVerificationAction $updateAction,
        private readonly DeleteDocumentVerificationAction $deleteAction,
        private readonly StartDocumentVerificationAction  $startAction,
        private readonly CompleteDocumentVerificationAction $completeAction,
        private readonly ApproveDocumentVerificationAction  $approveAction,
        private readonly RejectDocumentVerificationAction   $rejectAction,
    ) {}

    public function index(GetAllDocumentVerificationRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            DocumentVerificationResource::class
        );

        return $this->responseSuccess($result, 'Document verifications retrieved successfully');
    }

    public function show(GetDocumentVerificationRequest $request, DocumentVerification $documentVerification): JsonResponse
    {
        $result = $this->getAction->execute($documentVerification->id);

        return $this->responseSuccess(
            new DocumentVerificationResource($result),
            'Document verification retrieved successfully'
        );
    }

    public function store(StoreDocumentVerificationRequest $request): JsonResponse
    {
        $verification = $this->createAction->execute(
            DocumentVerificationMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new DocumentVerificationResource($verification),
            'Document verification created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(UpdateDocumentVerificationRequest $request, DocumentVerification $documentVerification): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $documentVerification,
            DocumentVerificationMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new DocumentVerificationResource($updated),
            'Document verification updated successfully'
        );
    }

    public function destroy(DeleteDocumentVerificationRequest $request, DocumentVerification $documentVerification): JsonResponse
    {
        $this->deleteAction->execute($documentVerification);

        return $this->responseSuccess(null, 'Document verification deleted successfully');
    }

    public function start(StartDocumentVerificationRequest $request, DocumentVerification $documentVerification): JsonResponse
    {
        $started = $this->startAction->execute(
            $documentVerification,
            $request->user()->id
        );

        return $this->responseSuccess(
            new DocumentVerificationResource($started),
            'Document verification started successfully'
        );
    }

    public function complete(CompleteDocumentVerificationRequest $request, DocumentVerification $documentVerification): JsonResponse
    {
        $completed = $this->completeAction->execute(
            $documentVerification,
            DocumentVerificationMapper::fromCompleteRequest($request)
        );

        return $this->responseSuccess(
            new DocumentVerificationResource($completed),
            'Document verification completed successfully'
        );
    }

    public function approve(ApproveDocumentVerificationRequest $request, DocumentVerification $documentVerification): JsonResponse
    {
        $approved = $this->approveAction->execute(
            $documentVerification,
            $request->user()->id,
            $request->validated('notes')
        );

        return $this->responseSuccess(
            new DocumentVerificationResource($approved),
            'Document verification approved successfully'
        );
    }

    public function reject(RejectDocumentVerificationRequest $request, DocumentVerification $documentVerification): JsonResponse
    {
        $rejected = $this->rejectAction->execute(
            $documentVerification,
            DocumentVerificationMapper::fromRejectRequest($request)
        );

        return $this->responseSuccess(
            new DocumentVerificationResource($rejected),
            'Document verification rejected successfully'
        );
    }
}