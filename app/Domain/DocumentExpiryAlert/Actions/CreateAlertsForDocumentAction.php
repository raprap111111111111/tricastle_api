<?php
// app/Domain/DocumentExpiryAlert/Actions/CreateAlertsForDocumentAction.php

namespace App\Domain\DocumentExpiryAlert\Actions;

use App\Models\ApplicantDocument;
use App\Models\DocumentExpiryAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CreateAlertsForDocumentAction
{
    private const THRESHOLDS = [
        '90_days' => 90,
        '60_days' => 60,
        '30_days' => 30,
    ];

    public function execute(ApplicantDocument $document): array
    {
        if (!$document->expiry_date) {
            return [];
        }

        $expiryDate = Carbon::parse($document->expiry_date);
        $today      = Carbon::today();
        $daysUntil  = (int) $today->diffInDays($expiryDate, false);

        $alerts = [];

        if ($daysUntil < 0) {
            $alert = $this->createOrGet($document, 'expired', $daysUntil, $expiryDate);
            if ($alert) $alerts[] = $alert;
            return $alerts;
        }

        foreach (self::THRESHOLDS as $alertType => $threshold) {
            if ($daysUntil <= $threshold) {
                $alert = $this->createOrGet($document, $alertType, $daysUntil, $expiryDate);
                if ($alert) $alerts[] = $alert;
            }
        }

        return $alerts;
    }

    private function createOrGet(
        ApplicantDocument $document,
        string $alertType,
        int $daysUntil,
        Carbon $expiryDate,
    ): ?DocumentExpiryAlert {
        try {
            return DocumentExpiryAlert::firstOrCreate(
                [
                    'applicant_document_id' => $document->id,
                    'alert_type'            => $alertType,
                ],
                [
                    'applicant_id'      => $document->applicant_id,
                    'days_until_expiry' => $daysUntil,
                    'expiry_date'       => $expiryDate->toDateString(),
                    'email_sent'        => false,
                    'notification_sent' => false,
                ],
            );
        } catch (\Throwable $e) {
            Log::error('[CreateAlertsForDocumentAction] Failed to create alert', [
                'document_id' => $document->id,
                'alert_type'  => $alertType,
                'error'       => $e->getMessage(),
            ]);
            return null;
        }
    }
}