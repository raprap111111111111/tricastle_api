<?php

namespace App\Http\Controllers\v1;

use App\Domain\Auth\Actions\ChangePasswordAction;
use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\Actions\LogoutAction;
use App\Domain\Auth\Actions\RegisterAction;
use App\Domain\Auth\Mappers\AuthMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\ChangePasswordRequest;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Http\Resources\v1\AuthResource;
use App\Http\Resources\v1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginAction $loginAction,
        private readonly RegisterAction $registerAction,
        private readonly LogoutAction $logoutAction,
        private readonly ChangePasswordAction $changePasswordAction,
    ) {}

    /**
     * POST /api/v1/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->loginAction->execute(
            AuthMapper::fromLoginRequest($request),
            $request
        );

        return $this->responseSuccess(
            new AuthResource($result),
            'Login successful'
        );
    }

    /**
     * POST /api/v1/auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->registerAction->execute(
            AuthMapper::fromRegisterRequest($request)
        );

        return $this->responseSuccess(
            new AuthResource($result),
            'Registration successful',
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * PUT /api/v1/auth/preferences
     * Update the authenticated user's theme + effects preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme_preference' => 'sometimes|string|in:default,dark,christmas,halloween,valentines',
            'effects_enabled'  => 'sometimes|boolean',
        ]);

        $user = $request->user();
        $user->update($validated);

        return $this->responseSuccess(
            new UserResource($user->fresh()),
            'Preferences updated successfully'
        );
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $logoutAll = (bool) $request->input('logout_all_devices', false);

        $this->logoutAction->execute($request->user(), $logoutAll);

        return $this->responseSuccess(
            null,
            $logoutAll ? 'Logged out from all devices' : 'Logout successful'
        );
    }

    /**
     * GET /api/v1/auth/profile
     */
    /**
     * GET /api/v1/auth/profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        // Force reload with correct guard
        $user->load('roles');

        return $this->responseSuccess(
            new UserResource($user),
            'Profile retrieved successfully'
        );
    }

    /**
     * PUT /api/v1/auth/change-password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->changePasswordAction->execute(
            $request->user(),
            AuthMapper::fromChangePasswordRequest($request)
        );

        return $this->responseSuccess(
            null,
            'Password changed successfully. Please login again.'
        );
    }
}
