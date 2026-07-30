<?php

namespace App\Http\Controllers\v1;

use App\Domain\DocumentVersion\Actions\CreateDocumentVersionAction;
use App\Domain\DocumentVersion\Actions\DeleteDocumentVersionAction;
use App\Domain\DocumentVersion\Actions\GetDocumentVersionAction;
use App\Domain\DocumentVersion\Actions\ListDocumentVersionsAction;
use App\Domain\DocumentVersion\Actions\SetCurrentDocumentVersionAction;
use App\Domain\DocumentVersion\Mappers\DocumentVersionMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\DocumentVersion\DeleteDocumentVersionRequest;
use App\Http\Requests\v1\DocumentVersion\GetAllDocumentVersionRequest;
use App\Http\Requests\v1\DocumentVersion\GetDocumentVersionRequest;
use App\Http\Requests\v1\DocumentVersion\SetCurrentDocumentVersionRequest;
use App\Http\Requests\v1\DocumentVersion\StoreDocumentVersionRequest;
use App\Http\Resources\v1\DocumentVersionResource;
use App\Models\DocumentVersion;
use Illuminate\Http\JsonResponse;

class DocumentVersionController extends Controller
{
    public function __construct(
        private readonly ListDocumentVersionsAction      $listAction,
        private readonly GetDocumentVersionAction        $getAction,
        private readonly CreateDocumentVersionAction     $createAction,
        private readonly DeleteDocumentVersionAction     $deleteAction,
        private readonly SetCurrentDocumentVersionAction $setCurrentAction,
    ) {}

    public function index(GetAllDocumentVersionRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            DocumentVersionResource::class
        );

        return $this->responseSuccess($result, 'Document versions retrieved successfully');
    }

    public function show(GetDocumentVersionRequest $request, DocumentVersion $documentVersion): JsonResponse
    {
        $result = $this->getAction->execute($documentVersion->id);

        return $this->responseSuccess(
            new DocumentVersionResource($result),
            'Document version retrieved successfully'
        );
    }

    public function store(StoreDocumentVersionRequest $request): JsonResponse
    {
        $version = $this->createAction->execute(
            DocumentVersionMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new DocumentVersionResource($version),
            'Document version uploaded successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function destroy(DeleteDocumentVersionRequest $request, DocumentVersion $documentVersion): JsonResponse
    {
        $this->deleteAction->execute($documentVersion);

        return $this->responseSuccess(null, 'Document version deleted successfully');
    }

    public function setCurrent(SetCurrentDocumentVersionRequest $request, DocumentVersion $documentVersion): JsonResponse
    {
        $updated = $this->setCurrentAction->execute($documentVersion);

        return $this->responseSuccess(
            new DocumentVersionResource($updated),
            'Document version set as current successfully'
        );
    }
}