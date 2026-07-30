<?php
// app/Domain/DocumentExpiryAlert/Actions/DismissDocumentExpiryAlertAction.php

namespace App\Domain\DocumentExpiryAlert\Actions;

use App\Domain\DocumentExpiryAlert\Repositories\DocumentExpiryAlertRepository;
use App\Models\DocumentExpiryAlert;

class DismissDocumentExpiryAlertAction
{
    public function __construct(
        private readonly DocumentExpiryAlertRepository $repository
    ) {}

    public function execute(DocumentExpiryAlert $alert): DocumentExpiryAlert
    {
        return $this->repository->update($alert->id, [
            'notification_sent'    => true,
            'notification_sent_at' => now(),
        ]);
    }
}