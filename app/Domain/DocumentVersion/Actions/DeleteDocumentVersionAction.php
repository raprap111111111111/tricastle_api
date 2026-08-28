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

        // 🎯 Environment-aware disk resolution
        // Priority:
        // 1) disk saved on the version record (if column exists)
        // 2) default disk from .env (FILESYSTEM_DISK)
        $disk = $documentVersion->disk
            ?? $documentVersion->storage_driver
            ?? config('filesystems.default', 'public');

        $path = $documentVersion->file_path;

        // ─── Delete physical file ──────────────────────────────
        if (!empty($path)) {
            try {
                $storage = Storage::disk($disk);

                if ($storage->exists($path)) {
                    $storage->delete($path);
                }
            } catch (\Throwable $e) {
                // Optional: log and continue so DB record can still be removed
                // Log::warning('Failed to delete document version file', [...]);
            }
        }

        $this->repository->delete($documentVersion->id);
    }
}