<?php

namespace App\Domain\User\Actions;

use App\Domain\User\DTOs\CreateUserDTO;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Services\UserService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUserAction
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserService $userService
    ) {}

    public function execute(CreateUserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $data = [
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
                'password' => $this->userService->hashPassword($dto->password),
                'is_active' => $dto->isActive,
                'email_verified_at' => now(),
            ];

            if ($dto->avatar) {
                $data['avatar'] = $this->userService->uploadAvatar($dto->avatar);
            }

            $user = $this->repository->create($data);

            if ($dto->role) {
                $user->assignRole($dto->role);
            }

            return $user->fresh(['roles', 'permissions']);
        });
    }
}
