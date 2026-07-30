<?php
// app/Domain/LoginHistory/Actions/CreateLoginHistoryAction.php

namespace App\Domain\LoginHistory\Actions;

use App\Domain\LoginHistory\DTOs\CreateLoginHistoryDTO;
use App\Domain\LoginHistory\Repositories\LoginHistoryRepository;
use App\Models\LoginHistory;

class CreateLoginHistoryAction
{
    public function __construct(
        private readonly LoginHistoryRepository $repository
    ) {}

    public function execute(CreateLoginHistoryDTO $dto): LoginHistory
    {
        return $this->repository->create([
            'user_id'        => $dto->userId,
            'ip_address'     => $dto->ipAddress,
            'user_agent'     => $dto->userAgent,
            'device_type'    => $dto->deviceType,
            'browser'        => $dto->browser,
            'platform'       => $dto->platform,
            'location'       => $dto->location,
            'status'         => $dto->status,
            'failure_reason' => $dto->failureReason,
            'login_method'   => $dto->loginMethod,
            'logged_in_at'   => $dto->loggedInAt ?? now(),
        ]);
    }
}