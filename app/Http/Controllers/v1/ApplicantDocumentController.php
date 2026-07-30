<?php

namespace App\Http\Controllers\v1;

use App\Domain\ApplicantDocument\Actions\ListDocumentBatchesAction;
use App\Domain\ApplicantDocument\Actions\DeleteApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\GetApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\GetApplicantFolderAction;
use App\Domain\ApplicantDocument\Actions\ListApplicantDocumentFoldersAction;
use App\Domain\ApplicantDocument\Actions\ListApplicantDocumentsAction;
use App\Domain\ApplicantDocument\Actions\RejectApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\UpdateApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\UploadApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\VerifyApplicantDocumentAction;
use App\Domain\ApplicantDocument\DTOs\UploadApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Mappers\ApplicantDocumentMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ApplicantDocument\DeleteApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\GetAllApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\GetApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\RejectApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\UpdateApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\UploadApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\UploadNewVersionRequest;
use App\Http\Requests\v1\ApplicantDocument\VerifyApplicantDocumentRequest;
use App\Http\Resources\v1\ApplicantDocumentResource;
use App\Models\ApplicantDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicantDocumentController extends Controller
{
    public function __construct(
        private readonly ListApplicantDocumentsAction      $listAction,
        private readonly ListDocumentBatchesAction         $listBatchesAction,
        private readonly ListApplicantDocumentFoldersAction $listFoldersAction,
        private readonly GetApplicantFolderAction          $getFolderAction,
        private readonly GetApplicantDocumentAction        $getAction,
        private readonly UploadApplicantDocumentAction     $uploadAction,
        private readonly UpdateApplicantDocumentAction     $updateAction,
        private readonly DeleteApplicantDocumentAction     $deleteAction,
        private readonly VerifyApplicantDocumentAction     $verifyAction,
        private readonly RejectApplicantDocumentAction     $rejectAction,
    ) {}

    // =========================================================================
    // Level 1 — Batches that contain documents
    // GET /applicant-documents/batches
    // =========================================================================
    public function batches(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $data = $this->listBatchesAction->execute($validated);

        return $this->responseSuccess($data, 'Batches retrieved successfully');
    }

    // =========================================================================
    // Level 2 — Applicant folders inside a batch
    // GET /applicant-documents/folders?batch_id=&search=&offset=&limit=
    // =========================================================================
    public function folders(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'batch_id' => ['nullable', 'integer', 'exists:batches,id'],
            'search'   => ['nullable', 'string',  'max:100'],
            'offset'   => ['nullable', 'integer', 'min:0'],
            'limit'    => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $result = $this->listFoldersAction->execute($validated);

        return $this->responseSuccess($result, 'Folders retrieved successfully');
    }

    // =========================================================================
    // Level 3 — Single applicant document folder
    // GET /applicant-documents/{applicantId}/folder
    // =========================================================================
    public function folder(int $applicantId): JsonResponse
    {
        $data = $this->getFolderAction->execute($applicantId);

        return $this->responseSuccess($data, 'Folder retrieved successfully');
    }

    // =========================================================================
    // Standard document CRUD
    // =========================================================================

    /**
     * List all documents.
     * GET /applicant-documents
     */
    public function index(GetAllApplicantDocumentRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            ApplicantDocumentResource::class
        );

        return $this->responseSuccess($result, 'Documents retrieved successfully');
    }

    /**
     * Show a single document.
     * GET /applicant-documents/{applicantDocument}
     */
    public function show(
        GetApplicantDocumentRequest $request,
        ApplicantDocument           $applicantDocument
    ): JsonResponse {
        $result = $this->getAction->execute($applicantDocument->id);

        return $this->responseSuccess(
            new ApplicantDocumentResource($result),
            'Document retrieved successfully'
        );
    }

    /**
     * Upload a new document.
     * POST /applicant-documents
     */
    public function store(UploadApplicantDocumentRequest $request): JsonResponse
    {
        $document = $this->uploadAction->execute(
            ApplicantDocumentMapper::fromUploadRequest($request)
        );

        return $this->responseSuccess(
            new ApplicantDocumentResource($document),
            'Document uploaded successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Upload a new version of an existing document.
     * POST /applicant-documents/{applicantDocument}/versions
     */
    public function uploadVersion(
        UploadNewVersionRequest $request,
        ApplicantDocument       $applicantDocument
    ): JsonResponse {
        $dto = new UploadApplicantDocumentDTO(
            applicantId:    $applicantDocument->applicant_id,
            documentTypeId: $applicantDocument->document_type_id,
            file:           $request->file('file'),
            documentDate:   $request->input('document_date', $applicantDocument->document_date),
            expiryDate:     $request->input('expiry_date',   $applicantDocument->expiry_date),
            priority:       $request->input('priority',      $applicantDocument->priority ?? 'normal'),
            notes:          $request->input('notes',         $applicantDocument->notes),
            metadata:       $applicantDocument->metadata,
            uploadedBy:     auth()->id(),
        );

        $newVersion = $this->uploadAction->execute($dto);

        return $this->responseSuccess(
            new ApplicantDocumentResource($newVersion),
            'New version uploaded successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Update a document.
     * PUT /applicant-documents/{applicantDocument}
     */
    public function update(
        UpdateApplicantDocumentRequest $request,
        ApplicantDocument              $applicantDocument
    ): JsonResponse {
        $updated = $this->updateAction->execute(
            $applicantDocument,
            ApplicantDocumentMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new ApplicantDocumentResource($updated),
            'Document updated successfully'
        );
    }

    /**
     * Delete a document.
     * DELETE /applicant-documents/{applicantDocument}
     */
    public function destroy(
        DeleteApplicantDocumentRequest $request,
        ApplicantDocument              $applicantDocument
    ): JsonResponse {
        $this->deleteAction->execute($applicantDocument);

        return $this->responseSuccess(null, 'Document deleted successfully');
    }

    /**
     * Verify a document.
     * POST /applicant-documents/{applicantDocument}/verify
     */
    public function verify(
        VerifyApplicantDocumentRequest $request,
        ApplicantDocument              $applicantDocument
    ): JsonResponse {
        $verified = $this->verifyAction->execute(
            $applicantDocument,
            ApplicantDocumentMapper::fromVerifyRequest($request)
        );

        return $this->responseSuccess(
            new ApplicantDocumentResource($verified),
            'Document verified successfully'
        );
    }

    /**
     * Reject a document.
     * POST /applicant-documents/{applicantDocument}/reject
     */
    public function reject(
        RejectApplicantDocumentRequest $request,
        ApplicantDocument              $applicantDocument
    ): JsonResponse {
        $rejected = $this->rejectAction->execute(
            $applicantDocument,
            ApplicantDocumentMapper::fromRejectRequest($request)
        );

        return $this->responseSuccess(
            new ApplicantDocumentResource($rejected),
            'Document rejected successfully'
        );
    }
}