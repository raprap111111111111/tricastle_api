<?php
// app/Domain/CorrectionRequest/Notifications/CorrectionRequestedNotification.php

namespace App\Domain\CorrectionRequest\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\CorrectionRequest;

class CorrectionRequestedNotification extends BaseNotification
{
    public function __construct(
        private readonly CorrectionRequest $correctionRequest
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => '📝 New Correction Request',
            'message'      => "Correction request #{$this->correctionRequest->request_code} has been submitted and is awaiting approval.",
            'action_url'   => "/correction-requests/{$this->correctionRequest->id}",
            'action_label' => 'Review Request',
            'meta'         => [
                'correction_request_id' => $this->correctionRequest->id,
                'request_code'          => $this->correctionRequest->request_code,
                'severity'              => $this->correctionRequest->severity,
            ],
        ];
    }
}