<?php
// app/Domain/DocumentExpiryAlert/Notifications/DocumentExpiringNotification.php

namespace App\Domain\DocumentExpiryAlert\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\DocumentExpiryAlert;

class DocumentExpiringNotification extends BaseNotification
{
    public function __construct(
        private readonly DocumentExpiryAlert $alert
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => "⚠️ Document Expiring in {$this->alert->days_until_expiry} Days",
            'message'      => "A document for applicant #{$this->alert->applicant_id} is expiring on {$this->alert->expiry_date}. Please take action.",
            'action_url'   => "/applicants/{$this->alert->applicant_id}/documents/{$this->alert->applicant_document_id}",
            'action_label' => 'View Document',
            'meta'         => [
                'alert_id'              => $this->alert->id,
                'applicant_id'          => $this->alert->applicant_id,
                'applicant_document_id' => $this->alert->applicant_document_id,
                'days_until_expiry'     => $this->alert->days_until_expiry,
                'alert_type'            => $this->alert->alert_type,
                'expiry_date'           => $this->alert->expiry_date,
            ],
        ];
    }
}