<?php

namespace App\Domain\FileRepository\Mappers;

use App\Domain\FileRepository\DTOs\UploadFileRepositoryDTO;
use App\Http\Requests\v1\FileRepository\UploadFileRepositoryRequest;

class FileRepositoryMapper
{
    public static function fromUploadRequest(UploadFileRepositoryRequest $request): UploadFileRepositoryDTO
    {
        return new UploadFileRepositoryDTO(
            file:          $request->file('file'),
            disk:          $request->validated('disk', 'local'),
            storageDriver: $request->validated('storage_driver', 'local'),
            isEncrypted:   (bool) $request->validated('is_encrypted', false),
            metadata:      $request->validated('metadata'),
            uploadedBy:    $request->user()?->id,
        );
    }
}