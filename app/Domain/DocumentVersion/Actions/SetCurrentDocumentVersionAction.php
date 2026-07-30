<?php

namespace App\Domain\DocumentVersion\Actions;

use App\Domain\DocumentVersion\Repositories\DocumentVersionRepository;
use App\Models\DocumentVersion;

class SetCurrentDocumentVersionAction
{
    public function __construct(
        private readonly DocumentVersionRepository $repository
    ) {}

    public function execute(DocumentVersion $documentVersion): DocumentVersion
    {
        // ─── Unset all current versions for this document ──────
        $this->repository->unsetCurrentForDocument(
            $documentVersion->applicant_document_id
        );

        // ─── Set this version as current ──────────────────────
        return $this->repository->update($documentVersion->id, [
            'is_current' => true,
        ]);
    }
}