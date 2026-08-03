<?php
// app/Http/Controllers/v1/UserController.php

namespace App\Http\Controllers\v1;

use App\Domain\User\Actions\AssignRolesAction;
use App\Domain\User\Actions\CreateUserAction;
use App\Domain\User\Actions\DeleteUserAction;
use App\Domain\User\Actions\GetUserAction;
use App\Domain\User\Actions\ListUsersAction;
use App\Domain\User\Actions\ToggleUserActiveAction;
use App\Domain\User\Actions\UpdateUserAction;
use App\Domain\User\Mappers\UserMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\User\AssignRolesRequest;
use App\Http\Requests\v1\User\DeleteUserRequest;
use App\Http\Requests\v1\User\GetAllUserRequest;
use App\Http\Requests\v1\User\StoreUserRequest;
use App\Http\Requests\v1\User\UpdateUserRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly ListUsersAction        $listAction,
        private readonly GetUserAction          $getAction,
        private readonly CreateUserAction       $createAction,
        private readonly UpdateUserAction       $updateAction,
        private readonly DeleteUserAction       $deleteAction,
        private readonly ToggleUserActiveAction $toggleAction,
        private readonly AssignRolesAction      $assignRolesAction,
    ) {}

    public function index(GetAllUserRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            UserResource::class
        );

        return $this->responseSuccess($result, 'Users retrieved successfully');
    }

    public function show(User $user): JsonResponse
    {
        $result = $this->getAction->execute($user->id);

        return $this->responseSuccess(
            new UserResource($result),
            'User retrieved successfully'
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->createAction->execute(
            UserMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new UserResource($user),
            'User created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $user,
            UserMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new UserResource($updated),
            'User updated successfully'
        );
    }

    public function destroy(DeleteUserRequest $request, User $user): JsonResponse
    {
        $this->deleteAction->execute($user);

        return $this->responseSuccess(null, 'User deleted successfully');
    }

    public function toggleActive(User $user): JsonResponse
    {
        $updated = $this->toggleAction->execute($user);

        return $this->responseSuccess(
            new UserResource($updated),
            $updated->is_active ? 'User activated' : 'User deactivated'
        );
    }

    public function assignRoles(AssignRolesRequest $request, User $user): JsonResponse
    {
        $updated = $this->assignRolesAction->execute(
            $user,
            $request->validated('roles', [])
        );

        return $this->responseSuccess(
            new UserResource($updated),
            'Roles assigned successfully'
        );
    }
}