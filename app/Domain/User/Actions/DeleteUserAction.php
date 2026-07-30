<?php

namespace App\Domain\User\Actions;

use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Services\UserService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUserAction
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserService $userService
    ) {}

    public function execute(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $this->userService->deleteAvatar($user->avatar);
            
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }
            
            return $this->repository->delete($user);
        });
    }
}
