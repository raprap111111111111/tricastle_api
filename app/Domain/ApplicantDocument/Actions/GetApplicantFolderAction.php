<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Models\Applicant;
use Illuminate\Support\Collection;

class GetApplicantFolderAction
{
    private const PENDING_STATUSES = [
        'pending_verification',
        'under_review',
        'requires_correction',
    ];

    public function execute(int $applicantId): array
    {
        $applicant = Applicant::query()
            ->without(['assignedStaff', 'creator', 'lifestyle', 'educations', 'employments', 'tattoos'])
            ->with([
                'documents' => fn ($q) => $q->orderByDesc('version'),
                'documents.documentType:id,name,code,category',
            ])
            ->findOrFail($applicantId);

        $groups = $applicant->documents
            ->groupBy('document_type_id')
            ->map(fn ($docs) => $this->transformGroup($docs))
            ->values();

        return [
            'applicant_id'    => $applicant->id,
            'applicant_code'  => $applicant->applicant_code,
            'applicant_name'  => $this->buildFullName($applicant),
            'applicant_email' => $applicant->email,
            'total_types'     => $groups->count(),
            'total_documents' => (int) $groups->sum('total_versions'),
            'has_pending'     => $groups->contains(
                fn ($g) => in_array($g['latest_status'], self::PENDING_STATUSES, true)
            ),
            'groups'          => $groups,
        ];
    }

    private function transformGroup(Collection $docs): array
    {
        $latest = $docs->first();

        return [
            'document_type_id'   => $latest->document_type_id,
            'document_type_name' => $latest->documentType?->name ?? 'Unknown',
            'document_type_code' => $latest->documentType?->code ?? '—',
            'total_versions'     => $docs->count(),
            'latest_status'      => $latest->status,
            'latest_version'     => $latest->version,
            'versions'           => $docs->map(fn ($d) => [
                'id'            => $d->id,
                'version'       => $d->version,
                'file_name'     => $d->file_name,
                'file_size'     => $d->file_size,
                'mime_type'     => $d->mime_type,
                'status'        => $d->status,
                'priority'      => $d->priority,
                'document_date' => $d->document_date,
                'expiry_date'   => $d->expiry_date,
                'is_expired'    => (bool) $d->is_expired,
                'is_current'    => (bool) $d->is_current_version,
                'uploaded_at'   => $d->created_at?->toIso8601String(),
            ])->values(),
        ];
    }

    private function buildFullName(Applicant $a): string
    {
        return trim(implode(' ', array_filter([
            $a->first_name,
            $a->middle_name,
            $a->last_name,
            $a->suffix,
        ])));
    }
}
