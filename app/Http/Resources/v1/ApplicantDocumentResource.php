<?php

namespace App\Http\Resources\v1;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ApplicantDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $path = $this->file_path ?? $this->path ?? $this->fileRepository?->file_path ?? null;
        $diskName = $this->disk ?? $this->fileRepository?->disk ?? config('filesystems.default', 'public');

        $fileUrl = null;

        if ($path) {
            try {
                /** @var FilesystemAdapter $disk */
                $disk = Storage::disk($diskName);

                if (in_array($diskName, ['r2', 's3']) && method_exists($disk, 'temporaryUrl')) {
                    $fileUrl = $disk->temporaryUrl($path, now()->addHours(4));
                } else {
                    $fileUrl = $disk->url($path);
                }
            } catch (\Throwable $e) {
            }
        }

        // 🎯 BULLETPROOF FIX: If URL is null OR points to /storage/ (which causes 404s on Render), OVERRIDE IT.
        if (!$fileUrl || !str_starts_with($fileUrl, 'http') || str_contains($fileUrl, '/storage/')) {
            if ($this->id) {
                // Force it through our secure PHP streaming endpoint
                $fileUrl = url("/api/v1/applicant-documents/{$this->id}/preview");
            }
        }

        // Only force HTTPS outside of local development
        if (
            $fileUrl &&
            str_starts_with($fileUrl, 'http://') &&
            !str_contains($fileUrl, 'localhost') &&
            !str_contains($fileUrl, '127.0.0.1')
        ) {
            $fileUrl = str_replace('http://', 'https://', $fileUrl);
        }

        return [
            'id'                  => $this->id,
            'applicant_id'        => $this->applicant_id,
            'document_type_id'    => $this->document_type_id,
            'file_repository_id'  => $this->file_repository_id,

            'file_name'           => $this->file_name ?? $this->fileRepository?->original_name,
            'file_type'           => $this->file_type ?? $this->fileRepository?->extension,
            'file_size'           => $this->file_size ?? $this->fileRepository?->file_size,
            'mime_type'           => $this->mime_type ?? $this->fileRepository?->mime_type,

            // 🎯 NOW GUARANTEED TO BE /PREVIEW IF CLOUD URL FAILS
            'file_url'            => $fileUrl,
            'url'                 => $fileUrl,
            'public_url'          => $fileUrl,
            'file_path'           => $path,

            'extracted_data'      => $this->extracted_data,
            'validated_data'      => $this->validated_data,
            'ocr_confidence'      => $this->ocr_confidence,

            'status'              => $this->status,
            'priority'            => $this->priority,

            'document_date'       => $this->document_date?->toDateString(),
            'expiry_date'         => $this->expiry_date?->toDateString(),
            'is_expired'          => $this->is_expired,
            'expiry_notified'     => $this->expiry_notified,

            'version'             => $this->version,
            'is_current_version'  => $this->is_current_version,

            'last_verified_at'    => $this->last_verified_at?->toDateTimeString(),

            'rejection_reason'    => $this->rejection_reason,
            'rejected_at'         => $this->rejected_at?->toDateTimeString(),

            'notes'               => $this->notes,
            'metadata'            => $this->metadata,

            'applicant'           => $this->whenLoaded('applicant', fn () => [
                'id'        => $this->applicant->id,
                'full_name' => $this->applicant->full_name,
                'email'     => $this->applicant->email,
            ]),
            'document_type'       => $this->whenLoaded('documentType', fn () => [
                'id'   => $this->documentType->id,
                'name' => $this->documentType->name,
                'code' => $this->documentType->code,
            ]),

            'created_at'          => $this->created_at?->toDateTimeString(),
            'updated_at'          => $this->updated_at?->toDateTimeString(),
        ];
    }
}