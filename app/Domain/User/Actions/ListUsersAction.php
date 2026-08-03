<?php
// app/Domain/User/Actions/ListUsersAction.php

namespace App\Domain\User\Actions;

use App\Domain\User\Repositories\UserRepository;

class ListUsersAction
{
    public function __construct(
        private readonly UserRepository $repository,
    ) {}

    public function execute(array $filters, string $resourceClass): array
    {
        $result = $this->repository->paginate($filters);

        return [
            'records'      => $resourceClass::collection($result['records']),
            'total'        => $result['total'],
            'offset'       => $result['offset'],
            'limit'        => $result['limit'],
            'per_page'     => $result['per_page'],
            'current_page' => $result['current_page'],
            'last_page'    => $result['last_page'],
        ];
    }
}