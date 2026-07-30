<?php

namespace App\Domain\DocumentType\Actions;

use App\Domain\DocumentType\Repositories\DocumentTypeRepository;
use App\Models\DocumentType;

class GetDocumentTypeAction
{
    public function __construct(
        private readonly DocumentTypeRepository $repository
    ) {}

    public function execute(int $id): DocumentType
    {
        return $this->repository->findOrFail($id);
    }
}