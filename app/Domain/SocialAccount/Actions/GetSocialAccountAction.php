<?php
// app/Domain/SocialAccount/Actions/GetSocialAccountAction.php

namespace App\Domain\SocialAccount\Actions;

use App\Domain\SocialAccount\Repositories\SocialAccountRepository;
use App\Models\SocialAccount;

class GetSocialAccountAction
{
    public function __construct(
        private readonly SocialAccountRepository $repository
    ) {}

    public function execute(int $id): SocialAccount
    {
        return $this->repository->findOrFail($id);
    }
}