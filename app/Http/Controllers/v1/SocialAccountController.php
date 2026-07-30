<?php
// app/Http/Controllers/v1/SocialAccountController.php

namespace App\Http\Controllers\v1;

use App\Domain\SocialAccount\Actions\CreateSocialAccountAction;
use App\Domain\SocialAccount\Actions\DeleteSocialAccountAction;
use App\Domain\SocialAccount\Actions\GetSocialAccountAction;
use App\Domain\SocialAccount\Actions\ListSocialAccountsAction;
use App\Domain\SocialAccount\Mappers\SocialAccountMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\SocialAccount\DeleteSocialAccountRequest;
use App\Http\Requests\v1\SocialAccount\GetAllSocialAccountRequest;
use App\Http\Requests\v1\SocialAccount\GetSocialAccountRequest;
use App\Http\Requests\v1\SocialAccount\StoreSocialAccountRequest;
use App\Http\Resources\v1\SocialAccountResource;
use App\Models\SocialAccount;
use Illuminate\Http\JsonResponse;

class SocialAccountController extends Controller
{
    public function __construct(
        private readonly ListSocialAccountsAction $listAction,
        private readonly GetSocialAccountAction   $getAction,
        private readonly CreateSocialAccountAction $createAction,
        private readonly DeleteSocialAccountAction $deleteAction,
    ) {}

    public function index(GetAllSocialAccountRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            SocialAccountResource::class
        );

        return $this->responseSuccess($result, 'Social accounts retrieved successfully');
    }

    public function show(GetSocialAccountRequest $request, SocialAccount $socialAccount): JsonResponse
    {
        return $this->responseSuccess(
            new SocialAccountResource($this->getAction->execute($socialAccount->id)),
            'Social account retrieved successfully'
        );
    }

    public function store(StoreSocialAccountRequest $request): JsonResponse
    {
        $result = $this->createAction->execute(
            SocialAccountMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new SocialAccountResource($result),
            'Social account created successfully',
            201
        );
    }

    public function destroy(DeleteSocialAccountRequest $request, SocialAccount $socialAccount): JsonResponse
    {
        $this->deleteAction->execute($socialAccount);

        return $this->responseSuccess(null, 'Social account deleted successfully');
    }
}