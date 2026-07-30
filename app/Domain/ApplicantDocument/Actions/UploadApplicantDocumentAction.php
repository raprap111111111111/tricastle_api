<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\DTOs\UploadApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Notifications\DocumentUploadedNotification;
use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Models\ApplicantDocument;
use App\Models\FileRepository;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UploadApplicantDocumentAction
{
    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(UploadApplicantDocumentDTO $dto): ApplicantDocument
    {
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
            'version'            => $version + 1,
            'is_current_version' => true,
            'priority'           => $dto->priority,
            'notes'              => $dto->notes,
            'metadata'           => $dto->metadata,
            'uploaded_by'        => $dto->uploadedBy,
        ]);

        // ─── Notify admins new document needs verification ─────
        User::permission('correction-request.viewAny')
            ->get()
            ->each(fn(User $admin) => $admin->notify(
                new DocumentUploadedNotification($document)
            ));

        return $document;
    }
}