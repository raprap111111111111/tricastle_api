<?php

namespace App\Actions\ApplicantDocument;

use App\Models\ApplicantDocument;

class GetApplicantDocumentFolderAction
{
    public function execute(int $applicantId): array
    {
        $documents = ApplicantDocument::with([
                'applicant',
                'documentType',
                'uploadedBy',
            ])
            ->where('applicant_id', $applicantId)
            ->get();

        $applicant = $documents->first()?->applicant;

        return [
            'applicant_id'   => $applicantId,
            'applicant_name' => $applicant
                ? trim($applicant->first_name . ' ' . $applicant->last_name)
                : 'Unknown',
            'total_files'    => $documents->count(),
            'documents'      => $documents->map(fn($doc) => [
                'id'              => $doc->id,
                'file_name'       => $doc->file_name,
                'file_path'       => $doc->file_path,
                'file_url'        => $doc->file_url,  // accessor or Storage::url
                'file_size'       => $doc->file_size,
                'mime_type'       => $doc->mime_type,
                'document_type'   => $doc->documentType?->name,
                'uploaded_by'     => $doc->uploadedBy?->name,
                'uploaded_at'     => $doc->created_at->format('Y-m-d H:i:s'),
                'notes'           => $doc->notes,
            ])->values()->toArray(),
        ];
    }
}