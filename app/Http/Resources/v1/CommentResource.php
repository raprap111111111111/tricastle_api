<?php

// app/Http/Resources/v1/CommentResource.php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'commentable_type' => $this->commentable_type,
            'commentable_id'   => $this->commentable_id,
            'content'          => $this->content,
            'parent_id'        => $this->parent_id,
            'is_internal'      => $this->is_internal,
            'is_reply'         => ! is_null($this->parent_id),
            'replies_count'    => $this->whenCounted('replies'),

            // Timestamps
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'deleted_at'       => $this->deleted_at,

            // Relations
            'user'             => $this->whenLoaded('user'),
            'parent'           => $this->whenLoaded('parent'),
            'replies'          => CommentResource::collection($this->whenLoaded('replies')),
            'commentable'      => $this->whenLoaded('commentable'),
        ];
    }
}