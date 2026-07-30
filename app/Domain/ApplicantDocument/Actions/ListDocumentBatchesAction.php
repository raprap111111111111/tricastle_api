<?php
// app/Domain/ApplicantDocument/Actions/ListDocumentBatchesAction.php

namespace App\Domain\ApplicantDocument\Actions;

use App\Models\Batch;

class ListDocumentBatchesAction
{
    private const PENDING_STATUSES = [
        'pending_verification',
        'under_review',
        'requires_correction',
    ];

    public function execute(array $filters = []): array
    {
        $search = $filters['search'] ?? null;

        $batches = Batch::query()
            ->whereHas('applicants', fn ($q) =>
                $q->whereHas('documents')
            )
            // ✅ single withCount call — no duplicate keys
            ->withCount([
                'applicants as applicants_with_docs_count' => fn ($q) =>
                    $q->whereHas('documents'),
            ])
            ->when($search, fn ($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return $batches
            ->map(fn (Batch $batch) => [
                'id'                         => $batch->id,
                'name'                       => $batch->name,
                'code'                       => $batch->code,
                'applicants_with_docs_count' => (int) $batch->applicants_with_docs_count,
                'has_pending'                => $this->hasPending($batch->id),
                'created_at'                 => $batch->created_at?->toISOString(),
            ])
            ->values()
            ->all();
    }

    private function hasPending(int $batchId): bool
    {
        // ✅ whereIn with proper statuses — not just 'pending'
        return Batch::where('id', $batchId)
            ->whereHas('applicants', fn ($q) =>
                $q->whereHas('documents', fn ($d) =>
                    $d->whereIn('status', self::PENDING_STATUSES)
                )
            )
            ->exists();
    }
}