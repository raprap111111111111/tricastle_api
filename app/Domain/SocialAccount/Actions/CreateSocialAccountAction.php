<?php
// app/Domain/SocialAccount/Actions/CreateSocialAccountAction.php

namespace App\Domain\SocialAccount\Actions;

use App\Domain\SocialAccount\DTOs\CreateSocialAccountDTO;
use App\Domain\SocialAccount\Repositories\SocialAccountRepository;
use App\Models\SocialAccount;

class CreateSocialAccountAction
{
    public function __construct(
        private readonly SocialAccountRepository $repository
    ) {}

    public function execute(CreateSocialAccountDTO $dto): SocialAccount
    {
        return $this->repository->create([
            'user_id'          => $dto->userId,
            'provider'         => $dto->provider,
            'provider_id'      => $dto->providerId,
            'avatar'           => $dto->avatar,
            'token'            => $dto->token,
            'refresh_token'    => $dto->refreshToken,
            'token_expires_at' => $dto->tokenExpiresAt,
        ]);
    }
}