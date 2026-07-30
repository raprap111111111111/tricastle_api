<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\RegisterDTO;
use App\Domain\Auth\Notifications\WelcomeNotification;
use App\Domain\Auth\Services\AuthService;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class RegisterAction
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthService    $authService,
    ) {}

    public function execute(RegisterDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            // ─── Create user ───────────────────────────────────
            $user = $this->userRepository->create([
                'first_name'        => $dto->firstName,
                'middle_name'       => $dto->middleName,
                'last_name'         => $dto->lastName,
                'email'             => $dto->email,
                'password'          => $this->authService->hashPassword($dto->password),
                'phone'             => $dto->phone,
                'mobile'            => $dto->mobile,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            // ─── Assign role if provided ───────────────────────
            if ($dto->role) {
                $user->assignRole($dto->role);
            }

            // ─── Create access token ───────────────────────────
            $token = $this->authService->createAccessToken($user, 'api');

            // ─── Notify user welcome ───────────────────────────
            $user->notify(new WelcomeNotification($user));

            return [
                'user'         => $user->fresh(['roles', 'permissions']),
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ];
        });
    }
}