<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\ApplicantDocument;

class DeleteApplicantDocumentAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(ApplicantDocument $document): void
    {
        // Capture data BEFORE deletion
        $applicant  = $document->applicant;
        $docType    = $document->documentType?->name ?? 'Document';
        $name       = $applicant ? "{$applicant->first_name} {$applicant->last_name}" : 'Unknown';
        $uploaderId = $document->uploaded_by;
        $assignedId = $applicant?->assigned_staff_id;

        // 🔔 Notify staff who can view documents
        $this->notifyWarning(
            permissions: 'document.viewAny',
            title:       '🗑️ Document Deleted',
            message:     "{$docType} for {$name} has been deleted.",
            module:      'document',
        );

        // 🔔 Notify uploader personally
        $this->notifyUser(
            user:     $uploaderId,
            title:    '⚠️ Your uploaded document was deleted',
            message:  "The {$docType} you uploaded for {$name} has been removed.",
            module:   'document',
            severity: 'warn',
        );

        // 🔔 Notify assigned staff (if different from uploader)
        if ($assignedId && $assignedId !== $uploaderId) {
            $this->notifyUser(
                user:     $assignedId,
                title:    '⚠️ Document Deleted',
                message:  "The {$docType} for {$name} has been deleted.",
                module:   'document',
                severity: 'warn',
            );
        }

        // Decrement file reference count
        if ($document->fileRepository) {
            $document->fileRepository->decrementReferenceCount();
        }

        $this->repository->delete($document->id);
    }
}