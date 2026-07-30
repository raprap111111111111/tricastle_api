<?php

namespace App\Domain\User\Actions;

use App\Domain\User\DTOs\UpdateUserDTO;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Services\UserService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserService $userService
    ) {}

    public function execute(User $user, UpdateUserDTO $dto): User
    {
        return DB::transaction(function () use ($user, $dto) {
            $data = array_filter([
                'first_name' => $dto->firstName,
                'middle_name' => $dto->middleName,
                'last_name' => $dto->lastName,
                'suffix' => $dto->suffix,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'mobile' => $dto->mobile,
                'employee_code' => $dto->employeeCode,
                'department' => $dto->department,
                'position' => $dto->position,
                'is_active' => $dto->isActive,
            ], fn($value) => $value !== null);

            if ($dto->password) {
                $data['password'] = $this->userService->hashPassword($dto->password);
            }

            if ($dto->avatar) {
                $this->userService->deleteAvatar($user->avatar);
                $data['avatar'] = $this->userService->uploadAvatar($dto->avatar);
            }

            $user = $this->repository->update($user, $data);

            if ($dto->role) {
                $user->syncRoles([$dto->role]);
            }

            return $user->fresh(['roles', 'permissions']);
        });
    }
}
