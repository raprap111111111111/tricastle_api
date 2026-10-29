<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'batch_id'        => $this->batch_id,
            'name'            => $this->name,
            'color'           => $this->color ?? 'blue',
            'max_slots'       => $this->max_slots,
            'applicant_count' => $this->applicant_batches_count ?? $this->applicantBatches()->count(),
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
