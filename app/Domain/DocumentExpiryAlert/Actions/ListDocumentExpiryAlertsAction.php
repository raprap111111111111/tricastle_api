<?php
// app/Domain/DocumentExpiryAlert/Actions/ListDocumentExpiryAlertsAction.php

namespace App\Domain\DocumentExpiryAlert\Actions;

use App\Domain\DocumentExpiryAlert\Repositories\DocumentExpiryAlertRepository;

class ListDocumentExpiryAlertsAction
{
    public function __construct(
        private readonly DocumentExpiryAlertRepository $repository
    ) {}

    public function execute(array $params, string $resource): mixed
    {
        return $this->repository->paginate($params, $resource);
    }
}