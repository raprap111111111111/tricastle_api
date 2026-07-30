<?php
// app/Domain/SocialAccount/Mappers/SocialAccountMapper.php

namespace App\Domain\SocialAccount\Mappers;

use App\Domain\SocialAccount\DTOs\CreateSocialAccountDTO;
use App\Http\Requests\v1\SocialAccount\StoreSocialAccountRequest;

class SocialAccountMapper
{
    public static function fromCreateRequest(StoreSocialAccountRequest $request): CreateSocialAccountDTO
    {
        return new CreateSocialAccountDTO(
            userId:          $request->validated('user_id'),
            provider:        $request->validated('provider'),
            providerId:      $request->validated('provider_id'),
            avatar:          $request->validated('avatar'),
            token:           $request->validated('token'),
            refreshToken:    $request->validated('refresh_token'),
            tokenExpiresAt:  $request->validated('token_expires_at'),
        );
    }
}