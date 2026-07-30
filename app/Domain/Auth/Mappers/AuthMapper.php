<?php

namespace App\Domain\Auth\Mappers;

use App\Domain\Auth\DTOs\ChangePasswordDTO;
use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\Auth\DTOs\RegisterDTO;
use App\Http\Requests\v1\Auth\ChangePasswordRequest;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Requests\v1\Auth\RegisterRequest;

class AuthMapper
{
    public static function fromLoginRequest(LoginRequest $request): LoginDTO
    {
        return new LoginDTO(
            email: $request->validated('email'),
            password: $request->validated('password'),
            deviceName: $request->validated('device_name', 'api'),
            rememberMe: (bool) $request->validated('remember_me', false),
        );
    }

    public static function fromRegisterRequest(RegisterRequest $request): RegisterDTO
    {
        return new RegisterDTO(
            firstName: $request->validated('first_name'),
            lastName: $request->validated('last_name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            middleName: $request->validated('middle_name'),
            phone: $request->validated('phone'),
            mobile: $request->validated('mobile'),
            role: $request->validated('role', 'staff'),
        );
    }

    public static function fromChangePasswordRequest(ChangePasswordRequest $request): ChangePasswordDTO
    {
        return new ChangePasswordDTO(
            currentPassword: $request->validated('current_password'),
            newPassword: $request->validated('new_password'),
        );
    }
}
