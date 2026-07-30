<?php

namespace App\Domain\FileRepository\Actions;

use App\Models\FileRepository;
use Illuminate\Support\Facades\Storage;

class PurgeFileRepositoryAction
{
    public function execute(FileRepository $file): void
    {
        // ─── Delete physical file from storage ─────────────────
        if (Storage::disk($file->disk)->exists($file->file_path)) {
            Storage::disk($file->disk)->delete($file->file_path);
        }

        // ─── Force delete from DB ──────────────────────────────
        $file->forceDelete();
    }
}