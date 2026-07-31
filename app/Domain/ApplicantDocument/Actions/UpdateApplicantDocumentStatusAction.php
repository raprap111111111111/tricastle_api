<?php
// app/Domain/ApplicantDocument/Actions/UpdateApplicantDocumentStatusAction.php

namespace App\Domain\ApplicantDocument\Actions;

use App\Models\ApplicantDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UpdateApplicantDocumentStatusAction
{
    /**
     * @param array{
     *     status: string,
     *     rejection_reason?: string|null,
     *     notes?: string|null
     * } $data
     */
    public function execute(
        ApplicantDocument $document,
        array $data,
        ?int $userId = null
    ): ApplicantDocument {
        $userId ??= Auth::id();

        if ($userId === null) {
            throw new RuntimeException('An authenticated user is required to update document status.');
        }

        return DB::transaction(function () use ($document, $data, $userId) {
            $status = $data['status'];

            $updates = [
                'status' => $status,
            ];

            if (array_key_exists('notes', $data)) {
                $updates['notes'] = $data['notes'];
            }

            switch ($status) {
                case 'verified':
                    $updates = array_merge($updates, [
                        'last_verified_at' => now(),
                        'last_verified_by' => $userId,
                        'rejection_reason' => null,
                        'rejected_by'      => null,
                        'rejected_at'      => null,
                        'is_expired'       => false,
                    ]);
                    break;

                case 'rejected':
                    $updates = array_merge($updates, [
                        'rejection_reason' => $data['rejection_reason'] ?? null,
                        'rejected_by'      => $userId,
                        'rejected_at'      => now(),
                        'is_expired'       => false,
                    ]);
                    break;

                case 'expired':
                    $updates['is_expired'] = true;
                    break;

                case 'uploaded':
                case 'pending_verification':
                case 'under_review':
                case 'requires_correction':
                    $updates = array_merge($updates, [
                        'rejection_reason' => null,
                        'rejected_by'      => null,
                        'rejected_at'      => null,
                        'is_expired'       => false,
                    ]);
                    break;
            }

            $document->update($updates);

            return $document->refresh();
        });
    }
}