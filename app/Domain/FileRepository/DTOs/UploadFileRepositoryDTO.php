<?php

namespace App\Domain\FileRepository\DTOs;

use Illuminate\Http\UploadedFile;

final class UploadFileRepositoryDTO
{
    public readonly string $disk;
    public readonly string $storageDriver;

    public function __construct(
        public readonly UploadedFile $file,
        ?string                      $disk          = null,
        ?string                      $storageDriver = null,
        public readonly bool         $isEncrypted   = false,
        public readonly ?array       $metadata      = null,
        public readonly ?int         $uploadedBy    = null,
    ) {
        // 🎯 Dynamically resolve from .env (FILESYSTEM_DISK) if not explicitly passed
        $defaultDisk = config('filesystems.default', 'public');

        $this->disk          = $disk ?? $defaultDisk;
        $this->storageDriver = $storageDriver ?? $defaultDisk;
    }
}