<?php
// app/Domain/DocumentExpiryAlert/Notifications/DocumentExpiredNotification.php

namespace App\Domain\DocumentExpiryAlert\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\DocumentExpiryAlert;

class DocumentExpiredNotification extends BaseNotification
{
    public function __construct(
        private readonly DocumentExpiryAlert $alert
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => '🚨 Document Expired',
            'message'      => "A document for applicant #{$this->alert->applicant_id} has expired on {$this->alert->expiry_date}. Immediate action required.",
            'action_url'   => "/applicants/{$this->alert->applicant_id}/documents/{$this->alert->applicant_document_id}",
            'action_label' => 'View Document',
            'meta'         => [
                'alert_id'              => $this->alert->id,
                'applicant_id'          => $this->alert->applicant_id,
                'applicant_document_id' => $this->alert->applicant_document_id,
                'alert_type'            => $this->alert->alert_type,
                'expiry_date'           => $this->alert->expiry_date,
            ],
        ];
    }
}