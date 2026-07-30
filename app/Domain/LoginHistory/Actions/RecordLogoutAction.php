<?php
// app/Domain/LoginHistory/Actions/RecordLogoutAction.php

namespace App\Domain\LoginHistory\Actions;

use App\Domain\LoginHistory\Repositories\LoginHistoryRepository;
use App\Models\LoginHistory;

class RecordLogoutAction
{
    public function __construct(
        private readonly LoginHistoryRepository $repository
    ) {}

    public function execute(LoginHistory $history): LoginHistory
    {
        return $this->repository->recordLogout($history);
    }
}