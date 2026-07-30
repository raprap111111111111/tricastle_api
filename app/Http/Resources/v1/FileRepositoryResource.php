<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileRepositoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'file_hash'        => $this->file_hash,
            'file_path'        => $this->file_path,
            'original_name'    => $this->original_name,
            'mime_type'        => $this->mime_type,
            'file_size'        => $this->file_size,
            'file_size_formatted' => $this->file_size_formatted,
            'extension'        => $this->extension,
            'disk'             => $this->disk,
            'storage_driver'   => $this->storage_driver,
            'reference_count'  => $this->reference_count,
            'is_encrypted'     => $this->is_encrypted,
            'metadata'         => $this->metadata,
            'uploaded_by'      => $this->uploaded_by,
            'uploader'         => $this->whenLoaded('uploader', fn () => [
                'id'    => $this->uploader->id,
                'name'  => $this->uploader->name,
                'email' => $this->uploader->email,
            ]),
            'created_at'       => $this->created_at?->toDateTimeString(),
            'updated_at'       => $this->updated_at?->toDateTimeString(),
        ];
    }
}