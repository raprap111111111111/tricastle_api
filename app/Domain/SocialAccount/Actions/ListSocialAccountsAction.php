<?php
// app/Domain/SocialAccount/Actions/ListSocialAccountsAction.php

namespace App\Domain\SocialAccount\Actions;

use App\Domain\SocialAccount\Repositories\SocialAccountRepository;

class ListSocialAccountsAction
{
    public function __construct(
        private readonly SocialAccountRepository $repository
    ) {}

    public function execute(array $params, string $resource): mixed
    {
        return $this->repository->paginate($params, $resource);
    }
}