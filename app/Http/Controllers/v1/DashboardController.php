<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1;

use App\Domain\Dashboard\Services\DashboardService;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\DashboardStatsRequest;
use App\Http\Requests\v1\Dashboard\RecentActivityRequest;
use App\Http\Requests\v1\Dashboard\TrendsRequest;
use App\Http\Resources\Dashboard\ActiveBatchResource;
use App\Http\Resources\Dashboard\ActivityResource;
use App\Http\Resources\Dashboard\AttentionResource;
use App\Http\Resources\Dashboard\DashboardStatsResource;
use App\Http\Resources\Dashboard\PipelineResource;
use App\Http\Resources\Dashboard\QuickStatsResource;
use App\Http\Resources\Dashboard\StatusBreakdownResource;
use App\Http\Resources\Dashboard\TrendDataResource;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $service,
    ) {}

    /** GET /api/v1/dashboard/stats */
    public function stats(DashboardStatsRequest $request): JsonResponse
    {
        return response()->json([
            'data' => new DashboardStatsResource(
                $this->service->getStats($request->validated()),
            ),
        ]);
    }

    /** GET /api/v1/dashboard/activities */
    public function activities(RecentActivityRequest $request): JsonResponse
    {
        return response()->json([
            'data' => ActivityResource::collection(
                $this->service->getRecentActivity(
                    limit: (int) $request->input('limit', 10),
                ),
            ),
        ]);
    }

    /** Alias kept for backwards compatibility. */
    public function recentActivity(RecentActivityRequest $request): JsonResponse
    {
        return $this->activities($request);
    }

    /** GET /api/v1/dashboard/trends?range=14d */
    public function trends(TrendsRequest $request): JsonResponse
    {
        return response()->json([
            'data' => new TrendDataResource(
                $this->service->getTrends($request->days()),
            ),
        ]);
    }

    /** GET /api/v1/dashboard/status-breakdown */
    public function statusBreakdown(): JsonResponse
    {
        return response()->json([
            'data' => new StatusBreakdownResource(
                $this->service->getStatusBreakdown(),
            ),
        ]);
    }

    /** GET /api/v1/dashboard/pipeline */
    public function pipeline(): JsonResponse
    {
        return response()->json([
            'data' => new PipelineResource(
                $this->service->getPipeline(),
            ),
        ]);
    }

    /** GET /api/v1/dashboard/active-batches */
    public function activeBatches(): JsonResponse
    {
        return response()->json([
            'data' => ActiveBatchResource::collection(
                $this->service->getActiveBatches(),
            ),
        ]);
    }

    /** GET /api/v1/dashboard/quick-stats */
    public function quickStats(): JsonResponse
    {
        return response()->json([
            'data' => new QuickStatsResource(
                $this->service->getQuickStats(),
            ),
        ]);
    }

    /** GET /api/v1/dashboard/attention */
    public function attention(): JsonResponse
    {
        return response()->json([
            'data' => new AttentionResource(
                $this->service->getAttention(),
            ),
        ]);
    }

    /** GET /api/v1/dashboard/overview */
    public function overview(): JsonResponse
    {
        return response()->json([
            'data' => [
                'stats'    => $this->service->getStats([]),
                'pipeline' => $this->service->getPipeline(),
            ],
        ]);
    }

    /** GET /api/v1/dashboard/birthdays */
    public function birthdays(): JsonResponse
    {
        return response()->json([
            'data' => $this->service->getBirthdays(),
        ]);
    }
}
