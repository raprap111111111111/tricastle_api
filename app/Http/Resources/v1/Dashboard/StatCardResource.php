<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use App\Domains\Dashboard\DTOs\StatCardDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property StatCardDTO $resource */
class StatCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}