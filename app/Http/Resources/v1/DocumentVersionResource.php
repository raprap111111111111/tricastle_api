<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentVersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'applicant_document_id' => $this->applicant_document_id,
            'version_number'        => $this->version_number,
            'file_name'             => $this->file_name,
            'file_size'             => $this->file_size,
            'file_size_formatted'   => $this->getFileSizeFormatted(),
            'mime_type'             => $this->mime_type,
            'extension'             => $this->getExtension(),
            'extracted_data'        => $this->extracted_data,
            'change_reason'         => $this->change_reason,
            'is_current'            => $this->is_current,

            // Relations
            'applicant_document'    => $this->whenLoaded('applicantDocument', fn () => [
                'id'        => $this->applicantDocument->id,
                'file_name' => $this->applicantDocument->file_name,
                'status'    => $this->applicantDocument->status,
            ]),
            'uploader'              => $this->whenLoaded('uploader', fn () => [
                'id'   => $this->uploader->id,
                'name' => $this->uploader->name,
            ]),

            'created_at'            => $this->created_at?->toDateTimeString(),
            'updated_at'            => $this->updated_at?->toDateTimeString(),
        ];
    }
}