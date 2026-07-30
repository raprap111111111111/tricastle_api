<?php
// app/Domain/DocumentExpiryAlert/DTOs/CreateDocumentExpiryAlertDTO.php

namespace App\Domain\DocumentExpiryAlert\DTOs;

final readonly class CreateDocumentExpiryAlertDTO
{
    public function __construct(
        public int     $applicantDocumentId,
        public int     $applicantId,
        public int     $daysUntilExpiry,
        public string  $alertType,
        public string  $expiryDate,
    ) {}
}