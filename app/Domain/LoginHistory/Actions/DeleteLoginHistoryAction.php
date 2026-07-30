<?php
// app/Domain/LoginHistory/Actions/DeleteLoginHistoryAction.php

namespace App\Domain\LoginHistory\Actions;

use App\Domain\LoginHistory\Repositories\LoginHistoryRepository;
use App\Models\LoginHistory;

class DeleteLoginHistoryAction
{
    public function __construct(
        private readonly LoginHistoryRepository $repository
    ) {}

    public function execute(LoginHistory $history): void
    {
        $this->repository->delete($history->id);
    }
}