<?php
// app/Domain/ApplicantDocument/Actions/GetExpiringDocumentsAction.php

namespace App\Domain\ApplicantDocument\Actions;

use App\Models\ApplicantDocument;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetExpiringDocumentsAction
{
    /**
     * Get all documents that are expiring or expired,
     * with derived alert status (no DB alerts table needed).
     *
     * @param  array{
     *   alert_type?: string|null,       // '30_days'|'60_days'|'90_days'|'expired'|null (=all)
     *   applicant_id?: int|null,
     *   document_type_id?: int|null,
     *   search?: string|null,
     *   order_by?: string,
     *   order_dir?: string,
     *   limit?: int,
     *   offset?: int,
     * }  $filters
     */
    public function execute(array $filters = []): array
    {
        $today   = Carbon::today();
        $in90    = $today->copy()->addDays(90);

        $query = ApplicantDocument::query()
            ->with(['applicant:id,first_name,last_name,applicant_code', 'documentType:id,name,code'])
            ->whereNotNull('expiry_date')
            ->where('is_current_version', true)
            ->where(function ($q) use ($today, $in90) {
                // Expired OR expiring within 90 days
                $q->where('expiry_date', '<=', $in90->toDateString());
            });

        // ─── Filter: applicant ────────────────────────
        if (!empty($filters['applicant_id'])) {
            $query->where('applicant_id', $filters['applicant_id']);
        }

        // ─── Filter: document type ────────────────────
        if (!empty($filters['document_type_id'])) {
            $query->where('document_type_id', $filters['document_type_id']);
        }

        // ─── Filter: search ───────────────────────────
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhereHas('applicant', function ($sub) use ($search) {
                      $sub->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('applicant_code', 'like', "%{$search}%");
                  });
            });
        }

        // ─── Filter: alert_type ───────────────────────
        if (!empty($filters['alert_type'])) {
            $alertType = $filters['alert_type'];

            if ($alertType === 'expired') {
                $query->where('expiry_date', '<', $today->toDateString());
            } elseif ($alertType === '30_days') {
                $query->whereBetween('expiry_date', [
                    $today->toDateString(),
                    $today->copy()->addDays(30)->toDateString(),
                ]);
            } elseif ($alertType === '60_days') {
                $query->whereBetween('expiry_date', [
                    $today->copy()->addDays(31)->toDateString(),
                    $today->copy()->addDays(60)->toDateString(),
                ]);
            } elseif ($alertType === '90_days') {
                $query->whereBetween('expiry_date', [
                    $today->copy()->addDays(61)->toDateString(),
                    $today->copy()->addDays(90)->toDateString(),
                ]);
            }
        }

        // ─── Sorting ──────────────────────────────────
        $orderBy  = $filters['order_by']  ?? 'expiry_date';
        $orderDir = $filters['order_dir'] ?? 'asc';
        $query->orderBy($orderBy, $orderDir);

        // ─── Pagination ───────────────────────────────
        $limit  = $filters['limit']  ?? 10;
        $offset = $filters['offset'] ?? 0;

        $total   = (clone $query)->count();
        $records = $query->skip($offset)->take($limit)->get();

        // ─── Enrich with derived alert data ───────────
        $records = $records->map(function (ApplicantDocument $doc) use ($today) {
            $expiryDate = Carbon::parse($doc->expiry_date);
            $daysUntil  = (int) $today->diffInDays($expiryDate, false);

            // Derive alert_type on the fly
            $alertType = match (true) {
                $daysUntil < 0   => 'expired',
                $daysUntil <= 30 => '30_days',
                $daysUntil <= 60 => '60_days',
                $daysUntil <= 90 => '90_days',
                default          => null,
            };

            // Attach as attributes (Eloquent supports this)
            $doc->setAttribute('days_until_expiry', $daysUntil);
            $doc->setAttribute('alert_type', $alertType);
            $doc->setAttribute('is_critical', $daysUntil <= 30);

            return $doc;
        });

        return [
            'records'      => $records,
            'total'        => $total,
            'offset'       => $offset,
            'limit'        => $limit,
            'current_page' => (int) floor($offset / $limit) + 1,
            'last_page'    => (int) ceil($total / $limit),
            'per_page'     => $limit,
            'has_more'     => ($offset + $limit) < $total,
        ];
    }
}