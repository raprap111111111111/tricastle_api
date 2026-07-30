<?php
// app/Http/Resources/v1/NotificationResource.php

namespace App\Http\Resources\v1;

use App\Domain\Notification\Mappers\NotificationMapper;
use App\Domain\Notification\DTOs\NotificationDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var NotificationDTO $dto */
        $dto = NotificationMapper::fromModel($this->resource);

        return [
            'id'   => $this->id,
            'type' => class_basename($this->type),

            'data' => [
                'title'        => $dto->title,
                'message'      => $dto->message,
                'action_url'   => $dto->action_url,
                'action_label' => $dto->action_label,
                'meta'         => $dto->meta,
            ],

            'is_read' => $this->is_read,
            'read_at' => $this->read_at?->toISOString(),

            'notifiable' => [
                'type' => class_basename($this->notifiable_type),
                'id'   => $this->notifiable_id,
            ],

            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}