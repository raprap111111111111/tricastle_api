<?php

// app/Http/Controllers/v1/SettingController.php

namespace App\Http\Controllers\v1;

use App\Domain\Setting\Actions\CreateSettingAction;
use App\Domain\Setting\Actions\DeleteSettingAction;
use App\Domain\Setting\Actions\GetSettingAction;
use App\Domain\Setting\Actions\ListSettingsAction;
use App\Domain\Setting\Actions\UpdateSettingAction;
use App\Domain\Setting\Mappers\SettingMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Setting\GetAllSettingRequest;
use App\Http\Requests\v1\Setting\GetSettingRequest;
use App\Http\Requests\v1\Setting\StoreSettingRequest;
use App\Http\Requests\v1\Setting\UpdateSettingRequest;
use App\Http\Resources\v1\SettingResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct(
        private readonly ListSettingsAction  $listSettingsAction,
        private readonly GetSettingAction    $getSettingAction,
        private readonly CreateSettingAction $createSettingAction,
        private readonly UpdateSettingAction $updateSettingAction,
        private readonly DeleteSettingAction $deleteSettingAction,
    ) {}

    public function index(GetAllSettingRequest $request): JsonResponse
    {
        $result = $this->listSettingsAction->execute($request->validated());

        return $this->responseSuccess(
            data:    SettingResource::collection($result),
            message: 'Settings retrieved successfully.',
        );
    }

    public function show(GetSettingRequest $request, Setting $setting): JsonResponse
    {
        $result = $this->getSettingAction->execute($setting);

        return $this->responseSuccess(
            data:    new SettingResource($result),
            message: 'Setting retrieved successfully.',
        );
    }

    public function store(StoreSettingRequest $request): JsonResponse
    {
        $dto    = SettingMapper::fromStoreRequest($request);
        $result = $this->createSettingAction->execute($dto);

        return $this->responseSuccess(
            data:    new SettingResource($result),
            message: 'Setting created successfully.',
            code:    201,
        );
    }

    public function update(UpdateSettingRequest $request, Setting $setting): JsonResponse
    {
        $dto    = SettingMapper::fromUpdateRequest($request);
        $result = $this->updateSettingAction->execute($setting, $dto);

        return $this->responseSuccess(
            data:    new SettingResource($result),
            message: 'Setting updated successfully.',
        );
    }

    public function destroy(Setting $setting): JsonResponse
    {
        $this->deleteSettingAction->execute($setting);

        return $this->responseSuccess(
            message: 'Setting deleted successfully.',
        );
    }
}