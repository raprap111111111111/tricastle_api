<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\DTOs\UpdateApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Models\ApplicantDocument;

class UpdateApplicantDocumentAction
{
    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(ApplicantDocument $document, UpdateApplicantDocumentDTO $dto): ApplicantDocument
    {
        return $this->repository->update($document->id, array_filter([
            'document_date'  => $dto->documentDate,
            'expiry_date'    => $dto->expiryDate,
            'priority'       => $dto->priority,
            'notes'          => $dto->notes,
            'metadata'       => $dto->metadata,
            'validated_data' => $dto->validatedData,
        ], fn ($value) => $value !== null));
    }
}