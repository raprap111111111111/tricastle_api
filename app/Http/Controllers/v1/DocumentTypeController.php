<?php

namespace App\Http\Controllers\v1;

use App\Domain\DocumentType\Actions\CreateDocumentTypeAction;
use App\Domain\DocumentType\Actions\DeleteDocumentTypeAction;
use App\Domain\DocumentType\Actions\GetDocumentTypeAction;
use App\Domain\DocumentType\Actions\ListDocumentTypesAction;
use App\Domain\DocumentType\Actions\UpdateDocumentTypeAction;
use App\Domain\DocumentType\Mappers\DocumentTypeMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\DocumentType\DeleteDocumentTypeRequest;
use App\Http\Requests\v1\DocumentType\GetAllDocumentTypeRequest;
use App\Http\Requests\v1\DocumentType\GetDocumentTypeRequest;
use App\Http\Requests\v1\DocumentType\StoreDocumentTypeRequest;
use App\Http\Requests\v1\DocumentType\UpdateDocumentTypeRequest;
use App\Http\Resources\v1\DocumentTypeResource;
use App\Models\DocumentType;
use Illuminate\Http\JsonResponse;

class DocumentTypeController extends Controller
{
    public function __construct(
        private readonly ListDocumentTypesAction  $listAction,
        private readonly GetDocumentTypeAction    $getAction,
        private readonly CreateDocumentTypeAction $createAction,
        private readonly UpdateDocumentTypeAction $updateAction,
        private readonly DeleteDocumentTypeAction $deleteAction,
    ) {}

    public function index(GetAllDocumentTypeRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            DocumentTypeResource::class
        );

        return $this->responseSuccess($result, 'Document types retrieved successfully');
    }

    public function show(GetDocumentTypeRequest $request, DocumentType $documentType): JsonResponse
    {
        $result = $this->getAction->execute($documentType->id);

        return $this->responseSuccess(
            new DocumentTypeResource($result),
            'Document type retrieved successfully'
        );
    }

    public function store(StoreDocumentTypeRequest $request): JsonResponse
    {
        $documentType = $this->createAction->execute(
            DocumentTypeMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new DocumentTypeResource($documentType),
            'Document type created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $documentType,
            DocumentTypeMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new DocumentTypeResource($updated),
            'Document type updated successfully'
        );
    }

    public function destroy(DeleteDocumentTypeRequest $request, DocumentType $documentType): JsonResponse
    {
        $this->deleteAction->execute($documentType);

        return $this->responseSuccess(
            null,
            'Document type deleted successfully'
        );
    }
}