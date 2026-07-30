<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\ChangePasswordDTO;
use App\Domain\Auth\Notifications\PasswordChangedNotification;
use App\Domain\Auth\Services\AuthService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangePasswordAction
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function execute(User $user, ChangePasswordDTO $dto): User
    {
        return DB::transaction(function () use ($user, $dto) {
            // ─── Verify current password ───────────────────────
            if (!$this->authService->verifyPassword($user, $dto->currentPassword)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Current password is incorrect.'],
                ]);
            }

            // ─── Update password ───────────────────────────────
            $user->update([
                'password'            => $this->authService->hashPassword($dto->newPassword),
                'password_changed_at' => now(),
            ]);

            // ─── Revoke all tokens ─────────────────────────────
            $this->authService->revokeAllTokens($user);

            // ─── Notify user password was changed ──────────────
            $user->notify(new PasswordChangedNotification($user));

            return $user->fresh();
        });
    }
}