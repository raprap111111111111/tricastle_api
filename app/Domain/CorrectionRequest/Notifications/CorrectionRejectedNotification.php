<?php
// app/Domain/CorrectionRequest/Notifications/CorrectionRejectedNotification.php

namespace App\Domain\CorrectionRequest\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\CorrectionRequest;

class CorrectionRejectedNotification extends BaseNotification
{
    public function __construct(
        private readonly CorrectionRequest $correctionRequest
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => '❌ Correction Request Rejected',
            'message'      => "Your correction request #{$this->correctionRequest->request_code} has been rejected.",
            'action_url'   => "/correction-requests/{$this->correctionRequest->id}",
            'action_label' => 'View Request',
            'meta'         => [
                'correction_request_id' => $this->correctionRequest->id,
                'request_code'          => $this->correctionRequest->request_code,
            ],
        ];
    }
}