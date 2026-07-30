<?php

namespace App\Domain\User\Mappers;

use App\Domain\User\DTOs\CreateUserDTO;
use App\Domain\User\DTOs\UpdateUserDTO;
use App\Http\Requests\v1\User\StoreUserRequest;
use App\Http\Requests\v1\User\UpdateUserRequest;

class UserMapper
{
    public static function fromCreateRequest(StoreUserRequest $request): CreateUserDTO
    {
        return new CreateUserDTO(
            firstName: $request->validated('first_name'),
            lastName: $request->validated('last_name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            middleName: $request->validated('middle_name'),
            suffix: $request->validated('suffix'),
            phone: $request->validated('phone'),
            mobile: $request->validated('mobile'),
            employeeCode: $request->validated('employee_code'),
            department: $request->validated('department'),
            position: $request->validated('position'),
            role: $request->validated('role'),
            avatar: $request->file('avatar'),
            isActive: (bool) $request->validated('is_active', true),
        );
    }

    public static function fromUpdateRequest(UpdateUserRequest $request): UpdateUserDTO
    {
        return new UpdateUserDTO(
            firstName: $request->validated('first_name'),
            lastName: $request->validated('last_name'),
            middleName: $request->validated('middle_name'),
            suffix: $request->validated('suffix'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
            mobile: $request->validated('mobile'),
            employeeCode: $request->validated('employee_code'),
            department: $request->validated('department'),
            position: $request->validated('position'),
            role: $request->validated('role'),
            password: $request->validated('password'),
            avatar: $request->file('avatar'),
            isActive: $request->has('is_active') ? (bool) $request->validated('is_active') : null,
        );
    }
}
