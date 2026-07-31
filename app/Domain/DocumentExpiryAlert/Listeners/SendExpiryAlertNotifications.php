<?php
// app/Domain/DocumentExpiryAlert/Listeners/SendExpiryAlertNotifications.php

namespace App\Domain\DocumentExpiryAlert\Listeners;

use App\Domain\ApplicantDocument\Events\ApplicantDocumentUploaded;
use App\Domain\DocumentExpiryAlert\Actions\CreateAlertsForDocumentAction;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendExpiryAlertNotifications implements ShouldQueue
{
    public function __construct(
        private readonly CreateAlertsForDocumentAction $action
    ) {}

    public function handle(ApplicantDocumentUploaded $event): void
    {
        // Re-run to ensure alerts exist and (optionally) send notifications
        // The Upload action already creates them synchronously, this is a safety net
        // for edge cases or you can extend this to send emails to applicants
    }
}