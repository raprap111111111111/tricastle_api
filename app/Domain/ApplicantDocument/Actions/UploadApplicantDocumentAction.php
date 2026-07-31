<?php
// app/Domain/ApplicantDocument/Actions/UploadApplicantDocumentAction.php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\DTOs\UploadApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Events\ApplicantDocumentUploaded;
use App\Domain\ApplicantDocument\Notifications\DocumentUploadedNotification;
use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Domain\DocumentExpiryAlert\Actions\CreateAlertsForDocumentAction;
use App\Models\ApplicantDocument;
use App\Models\DocumentVersion;
use App\Models\FileRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadApplicantDocumentAction
{
    public function __construct(
        private readonly ApplicantDocumentRepository $repository,
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
                // ─── Store physical file ───────────────────────────
                $filePath = Storage::disk('local')->putFile(
                    'documents/' . date('Y/m'),
                    $dto->file
                );

                // ─── Create FileRepository record ──────────────────
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

            // ─── Get version number ────────────────────────────────
            $version = ApplicantDocument::where('applicant_id', $dto->applicantId)
                ->where('document_type_id', $dto->documentTypeId)
                ->max('version') ?? 0;

            $newVersionNumber = $version + 1;

            // ─── Mark previous versions as not current ─────────────
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

            // ─── Mark older version records as not current ─────────
            $documentIds = ApplicantDocument::where('applicant_id', $dto->applicantId)
                ->where('document_type_id', $dto->documentTypeId)
                ->pluck('id')
                ->toArray();

            if (!empty($documentIds)) {
                DocumentVersion::whereIn('applicant_document_id', $documentIds)
                    ->update(['is_current' => false]);
            }

            // ─── Create version record ─────────────────────────────
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

            // ✅ ═════════════════════════════════════════════════════
            // ✅ NEW: Auto-create expiry alerts if document has expiry
            // ✅ ═════════════════════════════════════════════════════
            if ($dto->expiryDate) {
                try {
                    $alerts = $this->createAlertsAction->execute($document);

                    Log::info('[UploadApplicantDocumentAction] Expiry alerts created', [
                        'document_id'   => $document->id,
                        'expiry_date'   => $dto->expiryDate,
                        'alerts_count'  => count($alerts),
                        'alert_types'   => array_map(fn($a) => $a->alert_type, $alerts),
                    ]);
                } catch (\Throwable $e) {
                    // Don't fail the upload if alert creation fails
                    Log::error('[UploadApplicantDocumentAction] Failed to create expiry alerts', [
                        'document_id' => $document->id,
                        'error'       => $e->getMessage(),
                    ]);
                }
            }

            // ─── Notify admins new document needs verification ─────
            User::permission('correction-request.viewAny')
                ->get()
                ->each(fn(User $admin) => $admin->notify(
                    new DocumentUploadedNotification($document)
                ));

            return $document;
        });

        // ✅ Fire event AFTER transaction commits (safer for queued listeners)
        ApplicantDocumentUploaded::dispatch($document);

        return $document;
    }
}