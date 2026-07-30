<?php
// app/Domain/VerificationMismatch/Notifications/MismatchDetectedNotification.php

namespace App\Domain\VerificationMismatch\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\VerificationMismatch;

class MismatchDetectedNotification extends BaseNotification
{
    public function __construct(
        private readonly VerificationMismatch $mismatch
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => '🚨 Critical Mismatch Detected',
            'message'      => "A critical mismatch was found in field '{$this->mismatch->field_label}'. Expected: '{$this->mismatch->source_value}', Got: '{$this->mismatch->entered_value}'.",
            'action_url'   => "/verifications/{$this->mismatch->document_verification_id}/mismatches",
            'action_label' => 'Review Mismatch',
            'meta'         => [
                'mismatch_id'              => $this->mismatch->id,
                'document_verification_id' => $this->mismatch->document_verification_id,
                'field_name'               => $this->mismatch->field_name,
                'severity'                 => $this->mismatch->severity,
            ],
        ];
    }
}