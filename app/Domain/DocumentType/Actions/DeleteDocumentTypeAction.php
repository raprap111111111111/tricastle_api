<?php

namespace App\Domain\DocumentType\Actions;

use App\Domain\DocumentType\Repositories\DocumentTypeRepository;
use App\Models\DocumentType;

class DeleteDocumentTypeAction
{
    public function __construct(
        private readonly DocumentTypeRepository $repository
    ) {}

    public function execute(DocumentType $documentType): void
    {
        $this->repository->delete($documentType->id);
    }
}