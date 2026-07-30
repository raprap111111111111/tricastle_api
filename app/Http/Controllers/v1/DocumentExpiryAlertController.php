<?php
// app/Http/Controllers/v1/DocumentExpiryAlertController.php

namespace App\Http\Controllers\v1;

use App\Domain\DocumentExpiryAlert\Actions\CheckExpiringDocumentsAction;
use App\Domain\DocumentExpiryAlert\Actions\CreateDocumentExpiryAlertAction;
use App\Domain\DocumentExpiryAlert\Actions\DeleteDocumentExpiryAlertAction;
use App\Domain\DocumentExpiryAlert\Actions\DismissDocumentExpiryAlertAction;
use App\Domain\DocumentExpiryAlert\Actions\GetDocumentExpiryAlertAction;
use App\Domain\DocumentExpiryAlert\Actions\ListDocumentExpiryAlertsAction;
use App\Domain\DocumentExpiryAlert\Mappers\DocumentExpiryAlertMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\DocumentExpiryAlert\DeleteDocumentExpiryAlertRequest;
use App\Http\Requests\v1\DocumentExpiryAlert\DismissDocumentExpiryAlertRequest;
use App\Http\Requests\v1\DocumentExpiryAlert\GetAllDocumentExpiryAlertRequest;
use App\Http\Requests\v1\DocumentExpiryAlert\GetDocumentExpiryAlertRequest;
use App\Http\Requests\v1\DocumentExpiryAlert\StoreDocumentExpiryAlertRequest;
use App\Http\Resources\v1\DocumentExpiryAlertResource;
use App\Models\DocumentExpiryAlert;
use Illuminate\Http\JsonResponse;

class DocumentExpiryAlertController extends Controller
{
    public function __construct(
        private readonly ListDocumentExpiryAlertsAction  $listAction,
        private readonly GetDocumentExpiryAlertAction    $getAction,
        private readonly CreateDocumentExpiryAlertAction $createAction,
        private readonly DeleteDocumentExpiryAlertAction $deleteAction,
        private readonly DismissDocumentExpiryAlertAction $dismissAction,
        private readonly CheckExpiringDocumentsAction    $checkAction,
    ) {}

    public function index(GetAllDocumentExpiryAlertRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            DocumentExpiryAlertResource::class
        );

        return $this->responseSuccess($result, 'Document expiry alerts retrieved successfully');
    }

    public function show(GetDocumentExpiryAlertRequest $request, DocumentExpiryAlert $documentExpiryAlert): JsonResponse
    {
        return $this->responseSuccess(
            new DocumentExpiryAlertResource($this->getAction->execute($documentExpiryAlert->id)),
            'Document expiry alert retrieved successfully'
        );
    }

    public function store(StoreDocumentExpiryAlertRequest $request): JsonResponse
    {
        $result = $this->createAction->execute(
            DocumentExpiryAlertMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new DocumentExpiryAlertResource($result),
            'Document expiry alert created successfully',
            201
        );
    }

    public function destroy(DeleteDocumentExpiryAlertRequest $request, DocumentExpiryAlert $documentExpiryAlert): JsonResponse
    {
        $this->deleteAction->execute($documentExpiryAlert);

        return $this->responseSuccess(null, 'Document expiry alert deleted successfully');
    }

    public function dismiss(DismissDocumentExpiryAlertRequest $request, DocumentExpiryAlert $documentExpiryAlert): JsonResponse
    {
        $result = $this->dismissAction->execute($documentExpiryAlert);

        return $this->responseSuccess(
            new DocumentExpiryAlertResource($result),
            'Document expiry alert dismissed successfully'
        );
    }

    public function check(): JsonResponse
    {
        $count = $this->checkAction->execute();

        return $this->responseSuccess(
            ['notified_count' => $count],
            "{$count} expiry alerts have been sent"
        );
    }
}