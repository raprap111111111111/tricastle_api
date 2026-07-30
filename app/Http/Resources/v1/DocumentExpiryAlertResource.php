<?php
// app/Http/Resources/v1/DocumentExpiryAlertResource.php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentExpiryAlertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'applicant_document_id' => $this->applicant_document_id,
            'applicant_id'          => $this->applicant_id,
            'days_until_expiry'     => $this->days_until_expiry,
            'alert_type'            => $this->alert_type,
            'expiry_date'           => $this->expiry_date->toDateString(),
            'email_sent'            => $this->email_sent,
            'email_sent_at'         => $this->email_sent_at?->toISOString(),
            'notification_sent'     => $this->notification_sent,
            'notification_sent_at'  => $this->notification_sent_at?->toISOString(),

            'applicant_document'    => $this->whenLoaded('applicantDocument'),
            'applicant'             => $this->whenLoaded('applicant'),

            'created_at'            => $this->created_at->toISOString(),
            'updated_at'            => $this->updated_at->toISOString(),
        ];
    }
}