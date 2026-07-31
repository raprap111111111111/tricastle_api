<?php
// app/Domain/ApplicantDocument/Actions/GetApplicantFolderAction.php

namespace App\Domain\ApplicantDocument\Actions;

use App\Models\ApplicantDocument;
use Illuminate\Support\Facades\Storage;

class GetApplicantFolderAction
{
    /**
     * Get all documents for a given applicant, grouped as a folder view.
     */
    public function execute(int $applicantId): array
    {
        $documents = ApplicantDocument::with([
                'applicant',
                'documentType',
                'uploader', // ✅ match relation name in your model
            ])
            ->where('applicant_id', $applicantId)
            ->where('is_current_version', true)
            ->orderByDesc('created_at')
            ->get();

        $applicant = $documents->first()?->applicant;

        return [
            'applicant_id'   => $applicantId,
            'applicant_name' => $applicant
                ? trim(($applicant->first_name ?? '') . ' ' . ($applicant->last_name ?? ''))
                : 'Unknown',
            'applicant_code' => $applicant?->applicant_code,
            'total_files'    => $documents->count(),
            'documents'      => $documents->map(fn (ApplicantDocument $doc) => [
                'id'            => $doc->id,
                'file_name'     => $doc->file_name,
                'file_path'     => $doc->file_path,
                'file_url'      => $this->buildFileUrl($doc),
                'file_size'     => $doc->file_size,
                'mime_type'     => $doc->mime_type,
                'document_type' => $doc->documentType?->name,
                'document_code' => $doc->documentType?->code,
                'status'        => $doc->status,
                'version'       => $doc->version,
                'expiry_date'   => $doc->expiry_date?->format('Y-m-d'),
                'uploaded_by'   => $doc->uploader?->name,
                'uploaded_at'   => $doc->created_at?->format('Y-m-d H:i:s'),
                'notes'         => $doc->notes,
            ])->values()->toArray(),
        ];
    }

    /**
     * Build a file URL that works whether the file is on public, local, or S3 disk.
     */
    private function buildFileUrl(ApplicantDocument $doc): ?string
    {
        if (empty($doc->file_path)) {
            return null;
        }
        

        // Prefer the preview endpoint (auth-protected, works for all disks)
        return route('applicant-documents.preview', ['applicantDocument' => $doc->id], false);
    }
}