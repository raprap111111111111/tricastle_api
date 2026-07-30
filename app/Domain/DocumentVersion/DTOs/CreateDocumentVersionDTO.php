<?php

namespace App\Domain\DocumentVersion\DTOs;

use Illuminate\Http\UploadedFile;

final class CreateDocumentVersionDTO
{
    public function __construct(
        public readonly int          $applicantDocumentId,
        public readonly UploadedFile $file,
        public readonly ?string      $changeReason  = null,
        public readonly ?array       $extractedData = null,
        public readonly ?int         $uploadedBy    = null,
    ) {}
}