<?php
// app/Domain/DocumentExpiryAlert/Actions/DeleteDocumentExpiryAlertAction.php

namespace App\Domain\DocumentExpiryAlert\Actions;

use App\Domain\DocumentExpiryAlert\Repositories\DocumentExpiryAlertRepository;
use App\Models\DocumentExpiryAlert;

class DeleteDocumentExpiryAlertAction
{
    public function __construct(
        private readonly DocumentExpiryAlertRepository $repository
    ) {}

    public function execute(DocumentExpiryAlert $alert): void
    {
        $this->repository->delete($alert->id);
    }
}