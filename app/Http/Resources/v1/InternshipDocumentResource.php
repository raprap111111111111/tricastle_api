<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InternshipDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'document_type' => $this->document_type,
            'document_no'   => $this->document_no,
            'status'        => $this->status,
            'download_url'  => $this->download_url,
            'generated_by'  => $this->generated_by,
            'created_at'    => $this->created_at?->toISOString(),
        ];
    }
}