<?php

namespace App\Domain\ApplicantDocument\DTOs;

use Illuminate\Http\UploadedFile;

final class UploadApplicantDocumentDTO
{
    public function __construct(
        public readonly int          $applicantId,
        public readonly int          $documentTypeId,
        public readonly UploadedFile $file,
        public readonly ?string      $documentDate  = null,
        public readonly ?string      $expiryDate    = null,
        public readonly string       $priority      = 'normal',
        public readonly ?string      $notes         = null,
        public readonly ?array       $metadata      = null,
        public readonly ?int         $uploadedBy    = null,
    ) {}
}