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

    /**
     * @param  array{
     *   batch_id?: int|null,
     *   search?:   string|null,
     *   offset?:   int,
     *   limit?:    int,
     * } $filters
     */
    public function execute(array $filters = []): array
    {
        $batchId = isset($filters['batch_id']) ? (int) $filters['batch_id'] : null;
        $search  = $filters['search'] ?? null;
        $offset  = (int) ($filters['offset'] ?? 0);
        $limit   = (int) ($filters['limit']  ?? 15);

        $query = Applicant::query()
            ->whereHas('documents')
            // ── batch filter (only when batch_id is provided) ──────────────
            ->when($batchId, fn ($q) =>
                $q->whereHas('batches', fn ($b) =>
                    $b->where('batches.id', $batchId)
                )
            )
            // ── search ────────────────────────────────────────────────────
            ->when($search, fn ($q) =>
                $q->where(fn ($qq) =>
                    $qq->where('first_name',       'like', "%{$search}%")
                       ->orWhere('last_name',       'like', "%{$search}%")
                       ->orWhere('middle_name',     'like', "%{$search}%")
                       ->orWhere('email',           'like', "%{$search}%")
                       ->orWhere('applicant_code',  'like', "%{$search}%")
                )
            )
            ->orderBy('last_name')
            ->orderBy('first_name');

        // ── count before pagination ────────────────────────────────────────
        $total = (clone $query)->count();

        // ── paginated fetch ────────────────────────────────────────────────
        $applicants = $query
            ->without([
                'assignedStaff',
                'creator',
                'lifestyle',
                'educations',
                'employments',
                'tattoos',
            ])
            ->withCount([
                'documents as total_documents',
                'documents as pending_documents' => fn ($q) =>
                    $q->whereIn('status', self::PENDING_STATUSES),
            ])
            ->offset($offset)
            ->limit($limit)
            ->get();

        // ── distinct document type counts (single query) ───────────────────
        $typeCounts = DB::table('applicant_documents')
            ->whereIn('applicant_id', $applicants->pluck('id'))
            ->select(
                'applicant_id',
                DB::raw('COUNT(DISTINCT document_type_id) as total_types')
            )
            ->groupBy('applicant_id')
            ->pluck('total_types', 'applicant_id');

        // ── latest upload per applicant (single query) ─────────────────────
        $latestUploads = DB::table('applicant_documents')
            ->whereIn('applicant_id', $applicants->pluck('id'))
            ->select(
                'applicant_id',
                DB::raw('MAX(created_at) as latest_upload')
            )
            ->groupBy('applicant_id')
            ->pluck('latest_upload', 'applicant_id');

        // ── shape records ──────────────────────────────────────────────────
        $records = $applicants->map(fn (Applicant $a) => [
            'applicant_id'    => $a->id,
            'applicant_code'  => $a->applicant_code,
            'applicant_name'  => $this->buildFullName($a),
            'applicant_email' => $a->email,
            'total_types'     => (int) ($typeCounts[$a->id]    ?? 0),
            'total_documents' => (int)  $a->total_documents,
            'has_pending'     => $a->pending_documents > 0,
            'latest_upload'   => $latestUploads[$a->id] ?? null,
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