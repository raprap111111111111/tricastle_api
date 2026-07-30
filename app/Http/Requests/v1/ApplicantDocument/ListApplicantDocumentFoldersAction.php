<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Models\Applicant;
use Illuminate\Support\Facades\DB;

class ListApplicantDocumentFoldersAction
{
    private const PENDING_STATUSES = [
        'pending_verification',
        'under_review',
        'requires_correction',
    ];

    // ListApplicantDocumentFoldersAction.php
    public function execute(int $applicantId): array
    {
        $search = $filters['search'] ?? null;
        $offset = (int) ($filters['offset'] ?? 0);
        $limit  = (int) ($filters['limit']  ?? 15);

        $query = Applicant::query()
            ->whereHas('documents')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('first_name',      'like', "%{$search}%")
                        ->orWhere('last_name',     'like', "%{$search}%")
                        ->orWhere('middle_name',   'like', "%{$search}%")
                        ->orWhere('email',         'like', "%{$search}%")
                        ->orWhere('applicant_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name');

        $total = (clone $query)->count();

        $applicants = $query
            ->without(['assignedStaff', 'creator', 'lifestyle', 'educations', 'employments', 'tattoos'])
            ->withCount([
                'documents as total_documents',
                'documents as pending_documents' => fn($q) =>
                $q->whereIn('status', self::PENDING_STATUSES),
            ])
            ->offset($offset)
            ->limit($limit)
            ->get();

        // One query to count distinct document types per applicant
        $typeCounts = DB::table('applicant_documents')
            ->whereIn('applicant_id', $applicants->pluck('id'))
            ->select('applicant_id', DB::raw('COUNT(DISTINCT document_type_id) as total_types'))
            ->groupBy('applicant_id')
            ->pluck('total_types', 'applicant_id');

        $records = $applicants->map(fn(Applicant $a) => [
            'applicant_id'    => $a->id,
            'applicant_code'  => $a->applicant_code,
            'applicant_name'  => $this->buildFullName($a),
            'applicant_email' => $a->email,
            'total_types'     => (int) ($typeCounts[$a->id] ?? 0),
            'total_documents' => (int) $a->total_documents,
            'has_pending'     => $a->pending_documents > 0,
        ]);

        return [
            'records'      => $records,
            'total'        => $total,
            'offset'       => $offset,
            'limit'        => $limit,
            'current_page' => $limit > 0 ? (int) floor($offset / $limit) + 1 : 1,
            'last_page'    => $limit > 0 ? max(1, (int) ceil($total / $limit)) : 1,
            'per_page'     => $limit,
            'has_more'     => ($offset + $limit) < $total,
        ];
    }

    private function buildFullName(Applicant $applicant): string
    {
        return trim(implode(' ', array_filter([
            $applicant->first_name,
            $applicant->middle_name,
            $applicant->last_name,
            $applicant->suffix,
        ])));
    }
}
