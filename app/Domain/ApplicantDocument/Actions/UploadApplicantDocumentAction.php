<?php
// app/Domain/ApplicantDocument/Actions/UploadApplicantDocumentAction.php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\DTOs\UploadApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Events\ApplicantDocumentUploaded;
use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Domain\DocumentExpiryAlert\Actions\CreateAlertsForDocumentAction;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\ApplicantDocument;
use App\Models\DocumentVersion;
use App\Models\FileRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadApplicantDocumentAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly ApplicantDocumentRepository   $repository,
        private readonly CreateAlertsForDocumentAction $createAlertsAction,
    ) {}

    public function execute(UploadApplicantDocumentDTO $dto): ApplicantDocument
    {
        $document = DB::transaction(function () use ($dto) {

            // ─── Generate file hash ────────────────────────────────
            $fileHash = hash_file('sha256', $dto->file->getRealPath());

            // ─── Check FileRepository for deduplication ────────────
            $fileRecord = FileRepository::where('file_hash', $fileHash)->first();

            if ($fileRecord) {
                $fileRecord->incrementReferenceCount();
                $filePath = $fileRecord->file_path;
            } else {
                $filePath = Storage::disk('local')->putFile(
                    'documents/' . date('Y/m'),
                    $dto->file
                );

                $fileRecord = FileRepository::create([
                    'file_hash'       => $fileHash,
                    'file_path'       => $filePath,
                    'original_name'   => $dto->file->getClientOriginalName(),
                    'mime_type'       => $dto->file->getMimeType(),
                    'file_size'       => $dto->file->getSize(),
                    'disk'            => 'local',
                    'storage_driver'  => 'local',
                    'reference_count' => 1,
                    'uploaded_by'     => $dto->uploadedBy,
                ]);
            }

            // ─── Version handling ─────────────────────────────────
            $version = ApplicantDocument::where('applicant_id', $dto->applicantId)
                ->where('document_type_id', $dto->documentTypeId)
                ->max('version') ?? 0;

            $newVersionNumber = $version + 1;

            ApplicantDocument::where('applicant_id', $dto->applicantId)
                ->where('document_type_id', $dto->documentTypeId)
                ->where('is_current_version', true)
                ->update(['is_current_version' => false]);

            // ─── Create document record ────────────────────────────
            $document = $this->repository->create([
                'applicant_id'       => $dto->applicantId,
                'document_type_id'   => $dto->documentTypeId,
                'file_repository_id' => $fileRecord->id,
                'file_path'          => $filePath,
                'file_name'          => $dto->file->getClientOriginalName(),
                'file_type'          => $dto->file->getClientOriginalExtension(),
                'file_size'          => $dto->file->getSize(),
                'mime_type'          => $dto->file->getMimeType(),
                'file_hash'          => $fileHash,
                'status'             => 'pending_verification',
                'document_date'      => $dto->documentDate,
                'expiry_date'        => $dto->expiryDate,
                'is_expired'         => false,
                'version'            => $newVersionNumber,
                'is_current_version' => true,
                'priority'           => $dto->priority,
                'notes'              => $dto->notes,
                'metadata'           => $dto->metadata,
                'uploaded_by'        => $dto->uploadedBy,
            ]);

            // ─── Version tracking ─────────────────────────────────
            $documentIds = ApplicantDocument::where('applicant_id', $dto->applicantId)
                ->where('document_type_id', $dto->documentTypeId)
                ->pluck('id')
                ->toArray();

            if (!empty($documentIds)) {
                DocumentVersion::whereIn('applicant_document_id', $documentIds)
                    ->update(['is_current' => false]);
            }

            DocumentVersion::create([
                'applicant_document_id' => $document->id,
                'version_number'        => $newVersionNumber,
                'file_path'             => $filePath,
                'file_name'             => $dto->file->getClientOriginalName(),
                'file_size'             => $dto->file->getSize(),
                'mime_type'             => $dto->file->getMimeType(),
                'is_current'            => true,
                'uploaded_by'           => $dto->uploadedBy,
                'change_reason'         => $newVersionNumber === 1
                    ? 'Initial upload'
                    : 'New version uploaded',
            ]);

            // ─── Auto-create expiry alerts ─────────────────────────
            if ($dto->expiryDate) {
                try {
                    $alerts = $this->createAlertsAction->execute($document);

                    Log::info('[UploadApplicantDocumentAction] Expiry alerts created', [
                        'document_id'  => $document->id,
                        'alerts_count' => count($alerts),
                    ]);
                } catch (\Throwable $e) {
                    Log::error('[UploadApplicantDocumentAction] Failed to create expiry alerts', [
                        'document_id' => $document->id,
                        'error'       => $e->getMessage(),
                    ]);
                }
            }

            return $document;
        });

        // ✅ Fire event AFTER transaction commits
        ApplicantDocumentUploaded::dispatch($document);

        // 🔔 Send notifications
        $applicant = $document->applicant;
        $docType   = $document->documentType?->name ?? 'Document';
        $name      = $applicant ? "{$applicant->first_name} {$applicant->last_name}" : 'Unknown';
        $code      = $applicant?->applicant_code ?? '';

        // Notify staff who can verify documents
        $this->notifyStaff(
            permissions: ['document.viewAny', 'correction-request.viewAny'],
            title:       '📄 New Document Uploaded',
            message:     "{$docType} was uploaded for {$name} ({$code}) and needs verification.",
            module:      'document',
            actionUrl:   "/documents/{$document->id}",
        );

        // Personal notification to assigned staff
        if ($applicant?->assigned_staff_id && $applicant->assigned_staff_id !== $dto->uploadedBy) {
            $this->notifyUser(
                user:      $applicant->assigned_staff_id,
                title:     '📎 New Document for Your Applicant',
                message:   "{$docType} was uploaded for {$name}.",
                module:    'document',
                actionUrl: "/documents/{$document->id}",
            );
        }

        return $document;
    }
}