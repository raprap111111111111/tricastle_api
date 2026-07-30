<?php
// app/Domain/DocumentExpiryAlert/Actions/CreateDocumentExpiryAlertAction.php

namespace App\Domain\DocumentExpiryAlert\Actions;

use App\Domain\DocumentExpiryAlert\DTOs\CreateDocumentExpiryAlertDTO;
use App\Domain\DocumentExpiryAlert\Repositories\DocumentExpiryAlertRepository;
use App\Models\DocumentExpiryAlert;

class CreateDocumentExpiryAlertAction
{
    public function __construct(
        private readonly DocumentExpiryAlertRepository $repository
    ) {}

    public function execute(CreateDocumentExpiryAlertDTO $dto): DocumentExpiryAlert
    {
        return $this->repository->create([
            'applicant_document_id' => $dto->applicantDocumentId,
            'applicant_id'          => $dto->applicantId,
            'days_until_expiry'     => $dto->daysUntilExpiry,
            'alert_type'            => $dto->alertType,
            'expiry_date'           => $dto->expiryDate,
            'email_sent'            => false,
            'notification_sent'     => false,
        ]);
    }
}