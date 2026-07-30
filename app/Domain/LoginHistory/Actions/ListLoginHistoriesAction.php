<?php
// app/Domain/LoginHistory/Actions/ListLoginHistoriesAction.php

namespace App\Domain\LoginHistory\Actions;

use App\Domain\LoginHistory\Repositories\LoginHistoryRepository;

class ListLoginHistoriesAction
{
    public function __construct(
        private readonly LoginHistoryRepository $repository
    ) {}

    public function execute(array $params, string $resource): mixed
    {
        return $this->repository->paginate($params, $resource);
    }
}