<?php
// app/Domain/CorrectionRequest/Notifications/CorrectionCompletedNotification.php

namespace App\Domain\CorrectionRequest\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\CorrectionRequest;

class CorrectionCompletedNotification extends BaseNotification
{
    public function __construct(
        private readonly CorrectionRequest $correctionRequest
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => '🎉 Correction Request Completed',
            'message'      => "Correction request #{$this->correctionRequest->request_code} has been completed successfully.",
            'action_url'   => "/correction-requests/{$this->correctionRequest->id}",
            'action_label' => 'View Request',
            'meta'         => [
                'correction_request_id' => $this->correctionRequest->id,
                'request_code'          => $this->correctionRequest->request_code,
            ],
        ];
    }
}