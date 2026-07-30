<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\AuthService;
use App\Models\User;

class LogoutAction
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function execute(User $user, bool $logoutAllDevices = false): void
    {
        if ($logoutAllDevices) {
            $this->authService->revokeAllTokens($user);
        } else {
            $this->authService->revokeCurrentToken($user);
        }

        // Update logout time in last login history
        $user->loginHistories()
            ->whereNull('logged_out_at')
            ->latest('logged_in_at')
            ->first()
            ?->update(['logged_out_at' => now()]);
    }
}
