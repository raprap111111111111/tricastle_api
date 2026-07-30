<?php
// app/Domain/ApplicantDocument/Notifications/DocumentUploadedNotification.php

namespace App\Domain\ApplicantDocument\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\ApplicantDocument;

class DocumentUploadedNotification extends BaseNotification
{
    public function __construct(
        private readonly ApplicantDocument $document
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => '📄 New Document Uploaded',
            'message'      => "A new document was uploaded for applicant #{$this->document->applicant_id} and is pending verification.",
            'action_url'   => "/applicants/{$this->document->applicant_id}/documents/{$this->document->id}",
            'action_label' => 'Review Document',
            'meta'         => [
                'document_id'      => $this->document->id,
                'applicant_id'     => $this->document->applicant_id,
                'document_type_id' => $this->document->document_type_id,
                'version'          => $this->document->version,
            ],
        ];
    }
}