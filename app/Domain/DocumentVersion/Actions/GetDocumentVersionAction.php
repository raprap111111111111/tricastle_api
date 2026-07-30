<?php

namespace App\Domain\DocumentVersion\Actions;

use App\Domain\DocumentVersion\Repositories\DocumentVersionRepository;
use App\Models\DocumentVersion;

class GetDocumentVersionAction
{
    public function __construct(
        private readonly DocumentVersionRepository $repository
    ) {}

    public function execute(int $id): DocumentVersion
    {
        return $this->repository->findOrFail($id);
    }
}