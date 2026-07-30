<?php

namespace App\Domain\FileRepository\DTOs;

use Illuminate\Http\UploadedFile;

final class UploadFileRepositoryDTO
{
    public function __construct(
        public readonly UploadedFile $file,
        public readonly string       $disk          = 'local',
        public readonly string       $storageDriver = 'local',
        public readonly bool         $isEncrypted   = false,
        public readonly ?array       $metadata      = null,
        public readonly ?int         $uploadedBy    = null,
    ) {}
}