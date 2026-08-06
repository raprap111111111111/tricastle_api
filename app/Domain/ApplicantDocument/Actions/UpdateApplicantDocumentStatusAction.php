<?php
// app/Domain/ApplicantDocument/Actions/UpdateApplicantDocumentStatusAction.php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\Notification\Traits\HasNotifications;
use App\Models\ApplicantDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UpdateApplicantDocumentStatusAction
{
    use HasNotifications;   // 🔔

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

        $updated = DB::transaction(function () use ($document, $data, $userId) {
            $status = $data['status'];

            $updates = ['status' => $status];

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

        // 🔔 Send status-based notifications
        $this->sendStatusNotifications($updated, $data['rejection_reason'] ?? null);

        return $updated;
    }

    /**
     * 🔔 Notify uploader + assigned staff about status change
     */
    private function sendStatusNotifications(ApplicantDocument $document, ?string $reason): void
    {
        $applicant = $document->applicant;
        $docType   = $document->documentType?->name ?? 'Document';
        $name      = $applicant ? "{$applicant->first_name} {$applicant->last_name}" : 'Unknown';

        // Message templates per status
        $configs = [
            'verified' => [
                'title'    => '✅ Document Verified',
                'message'  => "{$docType} for {$name} is now verified.",
                'severity' => 'success',
            ],
            'rejected' => [
                'title'    => '❌ Document Rejected',
                'message'  => "{$docType} for {$name} was rejected. Reason: " . ($reason ?? 'No reason provided'),
                'severity' => 'error',
            ],
            'expired' => [
                'title'    => '⏰ Document Expired',
                'message'  => "{$docType} for {$name} has expired.",
                'severity' => 'warn',
            ],
            'requires_correction' => [
                'title'    => '⚠️ Correction Required',
                'message'  => "{$docType} for {$name} needs correction.",
                'severity' => 'warn',
            ],
            'under_review' => [
                'title'    => '👀 Document Under Review',
                'message'  => "{$docType} for {$name} is being reviewed.",
                'severity' => 'info',
            ],
            'pending_verification' => [
                'title'    => '📄 Document Pending Verification',
                'message'  => "{$docType} for {$name} awaits verification.",
                'severity' => 'info',
            ],
        ];

        $cfg = $configs[$document->status] ?? null;
        if (!$cfg) return;

        // 🔔 Notify uploader
        $this->notifyUser(
            user:      $document->uploaded_by,
            title:     $cfg['title'],
            message:   $cfg['message'],
            module:    'document',
            actionUrl: "/documents/{$document->id}",
            severity:  $cfg['severity'],
        );

        // 🔔 Notify assigned staff (if different from uploader)
        if ($applicant?->assigned_staff_id && $applicant->assigned_staff_id !== $document->uploaded_by) {
            $this->notifyUser(
                user:      $applicant->assigned_staff_id,
                title:     $cfg['title'],
                message:   $cfg['message'],
                module:    'document',
                actionUrl: "/documents/{$document->id}",
                severity:  $cfg['severity'],
            );
        }
    }
}