<?php

namespace App\Domain\FileRepository\Actions;

use App\Domain\FileRepository\DTOs\UploadFileRepositoryDTO;
use App\Domain\FileRepository\Repositories\FileRepositoryRepository;
use App\Models\FileRepository;
use Illuminate\Support\Facades\Storage;

class UploadFileRepositoryAction
{
    public function __construct(
        private readonly FileRepositoryRepository $repository
    ) {}

    public function execute(UploadFileRepositoryDTO $dto): FileRepository
    {
        // ─── Generate file hash for deduplication ─────────────
        $fileHash = hash_file('sha256', $dto->file->getRealPath());

        // ─── Check if file already exists (deduplication) ─────
        $existing = $this->repository->findByHash($fileHash);

        if ($existing) {
            $existing->incrementReferenceCount();

            return $existing;
        }

        // ─── Store file ────────────────────────────────────────
        $path = Storage::disk($dto->disk)->putFile(
            'uploads/' . date('Y/m'),
            $dto->file
        );

        // ─── Create record ─────────────────────────────────────
        return $this->repository->create([
            'file_hash'       => $fileHash,
            'file_path'       => $path,
            'original_name'   => $dto->file->getClientOriginalName(),
            'mime_type'       => $dto->file->getMimeType(),
            'file_size'       => $dto->file->getSize(),
            'disk'            => $dto->disk,
            'storage_driver'  => $dto->storageDriver,
            'reference_count' => 1,
            'metadata'        => $dto->metadata,
            'is_encrypted'    => $dto->isEncrypted,
            'uploaded_by'     => $dto->uploadedBy,
        ]);
    }
}