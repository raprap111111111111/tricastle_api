<?php
// app/Domain/User/Actions/GetUserAction.php

namespace App\Domain\User\Actions;

use App\Domain\User\Repositories\UserRepository;
use App\Models\User;

class GetUserAction
{
    public function __construct(
        private readonly UserRepository $repository,
    ) {}

    public function execute(int $id): User
    {
        return $this->repository->findOrFail($id);
    }
}