<?php

namespace App\Domain\Auth\Mappers;

use App\Domain\Auth\DTOs\ChangePasswordDTO;
use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\Auth\DTOs\RegisterDTO;
use App\Http\Requests\v1\Auth\ChangePasswordRequest;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Domain\Auth\DTOs\UpdateProfileDTO;
use App\Http\Requests\v1\Auth\UpdateProfileRequest;

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
    
    public static function fromUpdateProfileRequest(UpdateProfileRequest $request): UpdateProfileDTO
    {
        return new UpdateProfileDTO(
            firstName: $request->input('first_name'),
            middleName: $request->input('middle_name'),
            lastName: $request->input('last_name'),
            suffix: $request->input('suffix'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            mobile: $request->input('mobile'),
            dateOfBirth: $request->input('date_of_birth'),
            gender: $request->input('gender'),
            department: $request->input('department'),
            position: $request->input('position'),
            address: $request->input('address'),
            city: $request->input('city'),
            province: $request->input('province'),
            country: $request->input('country'),
            postalCode: $request->input('postal_code'),
            bio: $request->input('bio'),
            avatar: $request->file('avatar'),
        );
    }
}
