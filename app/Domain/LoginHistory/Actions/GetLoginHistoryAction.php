<?php
// app/Domain/LoginHistory/Actions/GetLoginHistoryAction.php

namespace App\Domain\LoginHistory\Actions;

use App\Domain\LoginHistory\Repositories\LoginHistoryRepository;
use App\Models\LoginHistory;

class GetLoginHistoryAction
{
    public function __construct(
        private readonly LoginHistoryRepository $repository
    ) {}

    public function execute(int $id): LoginHistory
    {
        return $this->repository->findOrFail($id);
    }
}