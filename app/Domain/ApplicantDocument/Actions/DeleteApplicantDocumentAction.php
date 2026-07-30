<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Models\ApplicantDocument;

class DeleteApplicantDocumentAction
{
    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(ApplicantDocument $document): void
    {
        // Decrement file reference count
        if ($document->fileRepository) {
            $document->fileRepository->decrementReferenceCount();
        }

        $this->repository->delete($document->id);
    }
}