<?php
// app/Domain/ApplicantDocument/Notifications/DocumentVerifiedNotification.php

namespace App\Domain\ApplicantDocument\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\ApplicantDocument;

class DocumentVerifiedNotification extends BaseNotification
{
    public function __construct(
        private readonly ApplicantDocument $document
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => '✅ Document Verified',
            'message'      => "Document for applicant #{$this->document->applicant_id} has been successfully verified.",
            'action_url'   => "/applicants/{$this->document->applicant_id}/documents/{$this->document->id}",
            'action_label' => 'View Document',
            'meta'         => [
                'document_id'  => $this->document->id,
                'applicant_id' => $this->document->applicant_id,
                'verified_at'  => now()->toISOString(),
            ],
        ];
    }
}