<?php

namespace App\Http\Resources\v1;

use App\Enums\BatchStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Safely resolve status (works whether it's enum or string)
        $statusValue = $this->status instanceof \BackedEnum
            ? $this->status->value
            : (string) $this->status;

        // Try to get label if BatchStatus enum exists
        $statusLabel = null;
        if ($this->status instanceof \BackedEnum && method_exists($this->status, 'label')) {
            $statusLabel = $this->status->label();
        } elseif (class_exists(BatchStatus::class)) {
            $enum = BatchStatus::tryFrom($statusValue);
            $statusLabel = $enum?->label() ?? ucfirst($statusValue);
        } else {
            $statusLabel = ucfirst($statusValue);
        }

        return [
            'id'              => $this->id,
            'batch_number'    => $this->batch_number,
            'name'            => $this->name,
            'country'         => $this->country,
            'deployment_date' => $this->deployment_date?->toDateString(),
            'status'          => $statusValue,
            'status_label'    => $statusLabel,
            'is_active'       => (bool) $this->is_active,
            'description'     => $this->description,
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
            'deleted_at'      => $this->whenNotNull($this->deleted_at?->toIso8601String()),
        ];
    }
}