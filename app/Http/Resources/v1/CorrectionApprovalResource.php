<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CorrectionApprovalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'correction_request_id' => $this->correction_request_id,
            'approver_id'           => $this->approver_id,
            'decision'              => $this->decision,
            'comments'              => $this->comments,
            'conditions'            => $this->conditions,
            'approval_level'        => $this->approval_level,
            'level_label'           => $this->getLevelLabel(),
            'decided_at'            => $this->decided_at?->toDateTimeString(),

            // Status flags
            'is_pending'            => $this->isPending(),
            'is_approved'           => $this->isApproved(),
            'is_rejected'           => $this->isRejected(),
            'is_escalated'          => $this->isEscalated(),
            'is_decided'            => $this->isDecided(),
            'is_supervisor_level'   => $this->isSupervisorLevel(),
            'is_admin_level'        => $this->isAdminLevel(),

            // Relations
            'correction_request'    => $this->whenLoaded('correctionRequest', fn () => [
                'id'           => $this->correctionRequest->id,
                'request_code' => $this->correctionRequest->request_code,
                'status'       => $this->correctionRequest->status,
                'severity'     => $this->correctionRequest->severity,
            ]),
            'approver'              => $this->whenLoaded('approver', fn () => [
                'id'    => $this->approver->id,
                'name'  => $this->approver->name,
                'email' => $this->approver->email,
            ]),

            'created_at'            => $this->created_at?->toDateTimeString(),
            'updated_at'            => $this->updated_at?->toDateTimeString(),
        ];
    }
}