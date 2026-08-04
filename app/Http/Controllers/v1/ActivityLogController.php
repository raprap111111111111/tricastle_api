<?php

namespace App\Http\Controllers\v1;

use App\Domain\ActivityLog\Actions\GetActivityLogAction;
use App\Domain\ActivityLog\Actions\ListActivityLogsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ActivityLog\GetActivityLogRequest;
use App\Http\Requests\v1\ActivityLog\GetAllActivityLogRequest;
use App\Http\Resources\v1\ActivityLogResource;
use Illuminate\Http\JsonResponse;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct(
        private readonly ListActivityLogsAction $listAction,
        private readonly GetActivityLogAction   $getAction,
    ) {}

    public function index(GetAllActivityLogRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            ActivityLogResource::class
        );

        return $this->responseSuccess($result, 'Activity logs retrieved successfully');
    }

    public function show(GetActivityLogRequest $request, Activity $activityLog): JsonResponse
    {
        $result = $this->getAction->execute($activityLog->id);

        return $this->responseSuccess(
            new ActivityLogResource($result),
            'Activity log retrieved successfully'
        );
    }
}