<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\DTOs\UpdateApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\ApplicantDocument;

class UpdateApplicantDocumentAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(ApplicantDocument $document, UpdateApplicantDocumentDTO $dto): ApplicantDocument
    {
        $updated = $this->repository->update($document->id, array_filter([
            'document_date'  => $dto->documentDate,
            'expiry_date'    => $dto->expiryDate,
            'priority'       => $dto->priority,
            'notes'          => $dto->notes,
            'metadata'       => $dto->metadata,
            'validated_data' => $dto->validatedData,
        ], fn ($value) => $value !== null));

        $applicant = $updated->applicant;
        $docType   = $updated->documentType?->name ?? 'Document';
        $name      = $applicant ? "{$applicant->first_name} {$applicant->last_name}" : 'Unknown';

        // 🔔 Notify assigned staff about update
        $this->notifyUser(
            user:      $applicant?->assigned_staff_id,
            title:     '✏️ Document Updated',
            message:   "{$docType} for {$name} has been updated.",
            module:    'document',
            actionUrl: "/documents/{$updated->id}",
        );

        return $updated;
    }
}