<?php
// app/Domain/DocumentExpiryAlert/Actions/GetDocumentExpiryAlertAction.php

namespace App\Domain\DocumentExpiryAlert\Actions;

use App\Domain\DocumentExpiryAlert\Repositories\DocumentExpiryAlertRepository;
use App\Models\DocumentExpiryAlert;

class GetDocumentExpiryAlertAction
{
    public function __construct(
        private readonly DocumentExpiryAlertRepository $repository
    ) {}

    public function execute(int $id): DocumentExpiryAlert
    {
        return $this->repository->findOrFail($id);
    }
}