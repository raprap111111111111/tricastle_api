<?php
// app/Domain/LoginHistory/Mappers/LoginHistoryMapper.php

namespace App\Domain\LoginHistory\Mappers;

use App\Domain\LoginHistory\DTOs\CreateLoginHistoryDTO;
use App\Http\Requests\v1\LoginHistory\StoreLoginHistoryRequest;

class LoginHistoryMapper
{
    public static function fromCreateRequest(StoreLoginHistoryRequest $request): CreateLoginHistoryDTO
    {
        return new CreateLoginHistoryDTO(
            userId:        $request->validated('user_id'),
            ipAddress:     $request->validated('ip_address'),
            userAgent:     $request->validated('user_agent'),
            deviceType:    $request->validated('device_type'),
            browser:       $request->validated('browser'),
            platform:      $request->validated('platform'),
            location:      $request->validated('location'),
            status:        $request->validated('status')        ?? 'success',
            failureReason: $request->validated('failure_reason'),
            loginMethod:   $request->validated('login_method')  ?? 'email',
            loggedInAt:    $request->validated('logged_in_at'),
        );
    }
}