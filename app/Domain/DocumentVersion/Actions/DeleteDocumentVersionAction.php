<?php

namespace App\Domain\DocumentVersion\Actions;

use App\Domain\DocumentVersion\Repositories\DocumentVersionRepository;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\Storage;

class DeleteDocumentVersionAction
{
    public function __construct(
        private readonly DocumentVersionRepository $repository
    ) {}

    public function execute(DocumentVersion $documentVersion): void
    {
        // ─── Cannot delete current version ────────────────────
        if ($documentVersion->isCurrent()) {
            throw new \Exception('Cannot delete the current version. Set another version as current first.');
        }

        // ─── Delete physical file ──────────────────────────────
        if (Storage::disk('local')->exists($documentVersion->file_path)) {
            Storage::disk('local')->delete($documentVersion->file_path);
        }

        $this->repository->delete($documentVersion->id);
    }
}