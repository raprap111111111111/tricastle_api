<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use App\Domains\Dashboard\DTOs\DashboardStatsDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property DashboardStatsDTO $resource */
class DashboardStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_applicants'  => $this->resource->totalApplicants->toArray(),
            'pending_documents' => $this->resource->pendingDocuments->toArray(),
            'verified_today'    => $this->resource->verifiedToday->toArray(),
            'corrections'       => $this->resource->corrections->toArray(),
        ];
    }
}