<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\DTOs\VerifyApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\ApplicantDocument;

class VerifyApplicantDocumentAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(ApplicantDocument $document, VerifyApplicantDocumentDTO $dto): ApplicantDocument
    {
        $updated = $this->repository->update($document->id, array_filter([
            'status'           => 'verified',
            'last_verified_at' => now(),
            'last_verified_by' => $dto->verifiedBy,
            'notes'            => $dto->notes,
            'validated_data'   => $dto->validatedData,
            'rejection_reason' => null,
            'rejected_by'      => null,
            'rejected_at'      => null,
        ], fn($value) => $value !== null));

        $applicant = $updated->applicant;
        $docType   = $updated->documentType?->name ?? 'Document';
        $name      = $applicant ? "{$applicant->first_name} {$applicant->last_name}" : 'Unknown';

        // 🔔 Notify the uploader — their work was approved!
        $this->notifyUser(
            user:      $updated->uploaded_by,
            title:     '✅ Your document was verified',
            message:   "The {$docType} you uploaded for {$name} has been verified.",
            module:    'document',
            actionUrl: "/documents/{$updated->id}",
            severity:  'success',
        );

        // 🔔 Notify assigned staff (if different from uploader)
        if ($applicant?->assigned_staff_id && $applicant->assigned_staff_id !== $updated->uploaded_by) {
            $this->notifyUser(
                user:      $applicant->assigned_staff_id,
                title:     '✅ Document Verified',
                message:   "{$docType} for {$name} is now verified.",
                module:    'document',
                actionUrl: "/documents/{$updated->id}",
                severity:  'success',
            );
        }

        return $updated;
    }
}