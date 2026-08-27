<?php

namespace App\Http\Controllers\v1;

use App\Domain\ApplicantDocument\Actions\DeleteApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\GetApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\GetApplicantFolderAction;
use App\Domain\ApplicantDocument\Actions\ListApplicantDocumentFoldersAction;
use App\Domain\ApplicantDocument\Actions\ListApplicantDocumentsAction;
use App\Domain\ApplicantDocument\Actions\ListDocumentBatchesAction;
use App\Domain\ApplicantDocument\Actions\RejectApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\UpdateApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\UpdateApplicantDocumentStatusAction;
use App\Domain\ApplicantDocument\Actions\UploadApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\VerifyApplicantDocumentAction;
use App\Domain\ApplicantDocument\Actions\GetExpiringDocumentsAction;
use App\Domain\ApplicantDocument\DTOs\UploadApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Mappers\ApplicantDocumentMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ApplicantDocument\DeleteApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\GetAllApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\GetApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\RejectApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\UpdateApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\UpdateApplicantDocumentStatusRequest;
use App\Http\Requests\v1\ApplicantDocument\UploadApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\UploadNewVersionRequest;
use App\Http\Requests\v1\ApplicantDocument\VerifyApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\GetExpiringDocumentsRequest;
use App\Http\Resources\v1\ApplicantDocumentResource;
use App\Models\ApplicantDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicantDocumentController extends Controller
{
    public function __construct(
        private readonly ListApplicantDocumentsAction        $listAction,
        private readonly ListDocumentBatchesAction           $listBatchesAction,
        private readonly ListApplicantDocumentFoldersAction $listFoldersAction,
        private readonly GetApplicantFolderAction            $getFolderAction,
        private readonly GetApplicantDocumentAction          $getAction,
        private readonly UploadApplicantDocumentAction       $uploadAction,
        private readonly UpdateApplicantDocumentAction       $updateAction,
        private readonly DeleteApplicantDocumentAction       $deleteAction,
        private readonly VerifyApplicantDocumentAction       $verifyAction,
        private readonly RejectApplicantDocumentAction       $rejectAction,
        private readonly UpdateApplicantDocumentStatusAction $updateStatusAction,
        private readonly GetExpiringDocumentsAction          $getExpiringDocumentsAction,
    ) {}

    public function batches(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $data = $this->listBatchesAction->execute($validated);

        return $this->responseSuccess($data, 'Batches retrieved successfully');
    }

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

    public function folder(int $applicantId): JsonResponse
    {
        $data = $this->getFolderAction->execute($applicantId);

        return $this->responseSuccess($data, 'Folder retrieved successfully');
    }

    public function index(GetAllApplicantDocumentRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            ApplicantDocumentResource::class
        );

        return $this->responseSuccess($result, 'Documents retrieved successfully');
    }

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

    public function uploadVersion(
        UploadNewVersionRequest $request,
        ApplicantDocument       $applicantDocument
    ): JsonResponse {
        $dto = new UploadApplicantDocumentDTO(
            applicantId: $applicantDocument->applicant_id,
            documentTypeId: $applicantDocument->document_type_id,
            file: $request->file('file'),
            documentDate: $request->input('document_date', $applicantDocument->document_date),
            expiryDate: $request->input('expiry_date',   $applicantDocument->expiry_date),
            priority: $request->input('priority',      $applicantDocument->priority ?? 'normal'),
            notes: $request->input('notes',         $applicantDocument->notes),
            metadata: $applicantDocument->metadata,
            uploadedBy: auth()->id(),
        );

        $newVersion = $this->uploadAction->execute($dto);

        return $this->responseSuccess(
            new ApplicantDocumentResource($newVersion),
            'New version uploaded successfully',
            JsonResponse::HTTP_CREATED
        );
    }

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

    public function destroy(
        DeleteApplicantDocumentRequest $request,
        ApplicantDocument              $applicantDocument
    ): JsonResponse {
        $this->deleteAction->execute($applicantDocument);

        return $this->responseSuccess(null, 'Document deleted successfully');
    }

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

    public function updateStatus(
        UpdateApplicantDocumentStatusRequest $request,
        ApplicantDocument $applicantDocument
    ): JsonResponse {
        $updated = $this->updateStatusAction->execute(
            $applicantDocument,
            $request->validated(),
            $request->user()->id,
        );

        return $this->responseSuccess(
            new ApplicantDocumentResource($updated),
            'Document status updated successfully'
        );
    }

    /**
     * Stream file inline (Supports local, public, r2, and s3)
     * GET /applicant-documents/{applicantDocument}/file
     * GET /applicant-documents/{applicantDocument}/preview
     */
        /**
     * Stream file inline (Supports local, public, r2, and s3)
     * GET /api/v1/applicant-documents/{applicantDocument}/preview
     * GET /api/v1/applicant-documents/{applicantDocument}/file
     */
    public function preview(ApplicantDocument $applicantDocument)
    {
        [$diskInstance, $diskName] = $this->resolveDiskInfo($applicantDocument);

        if (!$diskInstance) {
            abort(404, 'File not found on storage server.');
        }

        $path = $applicantDocument->file_path;
        $mime = $applicantDocument->mime_type ?? 'image/jpeg';
        $filename = $applicantDocument->file_name ?? 'document';

        // 🎯 Stream directly from Cloud Storage (R2 / S3) or Local
        if (in_array($diskName, ['r2', 's3'])) {
            try {
                if ($diskInstance->providesTemporaryUrls()) {
                    return redirect()->away($diskInstance->temporaryUrl($path, now()->addMinutes(30)));
                }
            } catch (\Throwable $e) {}

            return response()->stream(
                function () use ($diskInstance, $path) {
                    $stream = $diskInstance->readStream($path);
                    if ($stream) {
                        fpassthru($stream);
                        if (is_resource($stream)) fclose($stream);
                    }
                },
                200,
                [
                    'Content-Type'        => $mime,
                    'Content-Disposition' => 'inline; filename="' . addslashes($filename) . '"',
                    'Cache-Control'       => 'public, max-age=86400',
                ]
            );
        }

        return response()->file(
            $diskInstance->path($path),
            [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($filename) . '"',
                'Cache-Control'       => 'public, max-age=86400',
            ]
        );
    }

    public function file(ApplicantDocument $applicantDocument)
    {
        return $this->preview($applicantDocument);
    }

    public function download(ApplicantDocument $applicantDocument): StreamedResponse
    {
        [$diskInstance] = $this->resolveDiskInfo($applicantDocument);

        if (!$diskInstance) {
            abort(404, 'File not found on server.');
        }

        return $diskInstance->download(
            $applicantDocument->file_path,
            $applicantDocument->file_name,
            [
                'Content-Type' => $applicantDocument->mime_type ?? 'application/octet-stream',
            ],
        );
    }

    public function expiring(GetExpiringDocumentsRequest $request): JsonResponse
    {
        $result = $this->getExpiringDocumentsAction->execute($request->validated());

        return $this->responseSuccess(
            $result,
            'Expiring documents retrieved successfully'
        );
    }

    /**
     * 🎯 FIX: Resolves disk checking ['r2', 's3', 'public', 'local'] in proper priority order
     */
    private function resolveDiskInfo(ApplicantDocument $doc): array
    {
        if (empty($doc->file_path)) {
            return [null, null];
        }

        $candidates = ['r2', 's3', 'public', 'local'];

        foreach ($candidates as $name) {
            try {
                $disk = Storage::disk($name);
                if ($disk->exists($doc->file_path)) {
                    return [$disk, $name];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return [null, null];
    }
}

function echoStream($stream) {
    if (!$stream) return;
    while (!feof($stream)) {
        echo fread($stream, 1024 * 8);
        flush();
    }
    fclose($stream);
}