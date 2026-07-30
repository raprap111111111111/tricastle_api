<?php
// app/Http/Controllers/v1/LoginHistoryController.php

namespace App\Http\Controllers\v1;

use App\Domain\LoginHistory\Actions\CreateLoginHistoryAction;
use App\Domain\LoginHistory\Actions\DeleteLoginHistoryAction;
use App\Domain\LoginHistory\Actions\GetLoginHistoryAction;
use App\Domain\LoginHistory\Actions\ListLoginHistoriesAction;
use App\Domain\LoginHistory\Actions\RecordLogoutAction;
use App\Domain\LoginHistory\Mappers\LoginHistoryMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\LoginHistory\DeleteLoginHistoryRequest;
use App\Http\Requests\v1\LoginHistory\GetAllLoginHistoryRequest;
use App\Http\Requests\v1\LoginHistory\GetLoginHistoryRequest;
use App\Http\Requests\v1\LoginHistory\StoreLoginHistoryRequest;
use App\Http\Resources\v1\LoginHistoryResource;
use App\Models\LoginHistory;
use Illuminate\Http\JsonResponse;

class LoginHistoryController extends Controller
{
    public function __construct(
        private readonly ListLoginHistoriesAction $listAction,
        private readonly GetLoginHistoryAction    $getAction,
        private readonly CreateLoginHistoryAction $createAction,
        private readonly DeleteLoginHistoryAction $deleteAction,
        private readonly RecordLogoutAction       $recordLogoutAction,
    ) {}

    public function index(GetAllLoginHistoryRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            LoginHistoryResource::class
        );

        return $this->responseSuccess($result, 'Login histories retrieved successfully');
    }

    public function show(GetLoginHistoryRequest $request, LoginHistory $loginHistory): JsonResponse
    {
        return $this->responseSuccess(
            new LoginHistoryResource($this->getAction->execute($loginHistory->id)),
            'Login history retrieved successfully'
        );
    }

    public function store(StoreLoginHistoryRequest $request): JsonResponse
    {
        $result = $this->createAction->execute(
            LoginHistoryMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new LoginHistoryResource($result),
            'Login history created successfully',
            201
        );
    }

    public function destroy(DeleteLoginHistoryRequest $request, LoginHistory $loginHistory): JsonResponse
    {
        $this->deleteAction->execute($loginHistory);

        return $this->responseSuccess(null, 'Login history deleted successfully');
    }

    public function recordLogout(LoginHistory $loginHistory): JsonResponse
    {
        $this->authorize('recordLogout', $loginHistory);

        $result = $this->recordLogoutAction->execute($loginHistory);

        return $this->responseSuccess(
            new LoginHistoryResource($result),
            'Logout recorded successfully'
        );
    }
}