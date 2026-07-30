<?php
// app/Domain/SocialAccount/Actions/DeleteSocialAccountAction.php

namespace App\Domain\SocialAccount\Actions;

use App\Domain\SocialAccount\Repositories\SocialAccountRepository;
use App\Models\SocialAccount;

class DeleteSocialAccountAction
{
    public function __construct(
        private readonly SocialAccountRepository $repository
    ) {}

    public function execute(SocialAccount $socialAccount): void
    {
        $this->repository->delete($socialAccount->id);
    }
}