<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1;

use App\Domains\Dashboard\Services\DashboardService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DashboardStatsRequest;
use App\Http\Requests\Dashboard\RecentActivityRequest;
use App\Http\Resources\Dashboard\ActivityResource;
use App\Http\Resources\Dashboard\DashboardStatsResource;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $service,
    ) {}

    /**
     * GET /api/v1/dashboard/stats
     */
    public function stats(DashboardStatsRequest $request): JsonResponse
    {
        $stats = $this->service->getStats($request->validated());

        return response()->json([
            'data' => new DashboardStatsResource($stats),
        ]);
    }

    /**
     * GET /api/v1/dashboard/recent-activity
     */
    public function recentActivity(RecentActivityRequest $request): JsonResponse
    {
        $activities = $this->service->getRecentActivity(
            limit: (int) $request->input('limit', 10),
        );

        return response()->json([
            'data' => ActivityResource::collection($activities),
        ]);
    }
}