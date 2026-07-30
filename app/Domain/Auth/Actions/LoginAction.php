<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\Auth\Services\AuthService;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthService $authService,
    ) {}

    public function execute(LoginDTO $dto, Request $request): array
    {
        return DB::transaction(function () use ($dto, $request) {
            $user = $this->userRepository->findByEmail($dto->email);

            // User doesn't exist
            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid email or password.'],
                ]);
            }

            // Account is locked
            if ($this->authService->isAccountLocked($user)) {
                $this->authService->recordLoginHistory($user, $request, 'blocked', 'Account locked');
                throw ValidationException::withMessages([
                    'email' => ['Account is temporarily locked. Try again later.'],
                ]);
            }

            // Account is inactive
            if (!$this->authService->isAccountActive($user)) {
                $this->authService->recordLoginHistory($user, $request, 'failed', 'Account inactive');
                throw ValidationException::withMessages([
                    'email' => ['Your account is inactive. Contact administrator.'],
                ]);
            }

            // Wrong password
            if (!$this->authService->verifyPassword($user, $dto->password)) {
                $this->authService->incrementFailedAttempts($user);
                $this->authService->recordLoginHistory($user, $request, 'failed', 'Wrong password');
                throw ValidationException::withMessages([
                    'email' => ['Invalid email or password.'],
                ]);
            }

            // Success - reset failed attempts
            $this->authService->resetFailedAttempts($user);
            $this->authService->updateLastLogin($user, $request->ip());
            $this->authService->recordLoginHistory($user, $request, 'success');

            // Create token
            $token = $this->authService->createAccessToken($user, $dto->deviceName);

            return [
                'user' => $user->fresh(['roles', 'permissions']),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
        });
    }
}
