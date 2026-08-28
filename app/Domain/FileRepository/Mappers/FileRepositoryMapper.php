<?php

namespace App\Domain\FileRepository\Mappers;

use App\Domain\FileRepository\DTOs\UploadFileRepositoryDTO;
use App\Http\Requests\v1\FileRepository\UploadFileRepositoryRequest;

class FileRepositoryMapper
{
    public static function fromUploadRequest(UploadFileRepositoryRequest $request): UploadFileRepositoryDTO
    {
        // 🎯 Environment-aware default disk from .env (FILESYSTEM_DISK)
        $defaultDisk = config('filesystems.default', 'public');

        return new UploadFileRepositoryDTO(
            file:          $request->file('file'),
            disk:          $request->validated('disk', $defaultDisk),
            storageDriver: $request->validated('storage_driver', $defaultDisk),
            isEncrypted:   (bool) $request->validated('is_encrypted', false),
            metadata:      $request->validated('metadata'),
            uploadedBy:    $request->user()?->id,
        );
    }
}