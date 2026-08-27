<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeploymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 🎯 Smart check: Detail View vs List View
        $isDetailView = $request->routeIs('*.show');

        // ═════════════════════════════════════════════════════════════════
        // ⚡ BASE FIELDS (Always included for the Deployments Table)
        // ═════════════════════════════════════════════════════════════════
        $data = [
            'id'           => $this->id,
            'applicant_id' => $this->applicant_id,
            'batch_id'     => $this->batch_id,
            'status'       => $this->status?->value ?? $this->status,

            'deployment_country'       => $this->deployment_country,
            'deployment_company'       => $this->deployment_company,
            'deployment_position'      => $this->deployment_position,
            'deployed_at'              => $this->deployed_at?->toIso8601String(),
            'cancelled_at'             => $this->cancelled_at?->toIso8601String(),
            'cancellation_reason'      => $this->cancellation_reason,

            'contract_duration_months' => $this->contract_duration_months,
            'contract_start_date'      => $this->contract_start_date?->toDateString(),
            'contract_end_date'        => $this->contract_end_date?->toDateString(),

            'monthly_salary'   => $this->monthly_salary,
            'salary_currency'  => $this->salary_currency,

            'applicant' => $this->whenLoaded('applicant', fn () => [
                'id'              => $this->applicant->id,
                'applicant_code'  => $this->applicant->applicant_code,
                'first_name'      => $this->applicant->first_name,
                'middle_name'     => $this->applicant->middle_name,
                'last_name'       => $this->applicant->last_name,
                'full_name'       => trim("{$this->applicant->first_name} {$this->applicant->middle_name} {$this->applicant->last_name}"),
                'email'           => $this->applicant->email,
            ]),
        ];

        // ═════════════════════════════════════════════════════════════════
        // 🔍 FULL FIELDS (Included ONLY on GET /deployments/{id})
        // ═════════════════════════════════════════════════════════════════
        if ($isDetailView) {
            $data = array_merge($data, [
                'flight_date'      => $this->flight_date?->toDateString(),
                'visa_type'        => $this->visa_type,
                'deployment_notes' => $this->deployment_notes,
                'created_at'       => $this->created_at?->toIso8601String(),
                'updated_at'       => $this->updated_at?->toIso8601String(),

                // Add extra applicant info back in
                'applicant' => $this->whenLoaded('applicant', fn () => [
                    'id'              => $this->applicant->id,
                    'applicant_code'  => $this->applicant->applicant_code,
                    'first_name'      => $this->applicant->first_name,
                    'middle_name'     => $this->applicant->middle_name,
                    'last_name'       => $this->applicant->last_name,
                    'full_name'       => trim("{$this->applicant->first_name} {$this->applicant->middle_name} {$this->applicant->last_name}"),
                    'email'           => $this->applicant->email,
                    'mobile'          => $this->applicant->mobile,
                    'gender'          => $this->applicant->gender,
                    'nationality'     => $this->applicant->nationality,
                    'passport_number' => $this->applicant->passport_number,
                    'passport_expiry' => $this->applicant->passport_expiry,
                    'status'          => $this->applicant->status,
                ]),

                'batch' => $this->whenLoaded('batch', fn () => [
                    'id'           => $this->batch->id,
                    'name'         => $this->batch->name,
                    'batch_number' => $this->batch->batch_number,
                    'country'      => $this->batch->country ?? null,
                    'is_active'    => $this->batch->is_active ?? false,
                    'status'       => $this->batch->status ?? null,
                ]),

                'processed_by' => $this->whenLoaded('processedBy', fn () => [
                    'id'         => $this->processedBy->id,
                    'full_name'  => trim("{$this->processedBy->first_name} {$this->processedBy->last_name}"),
                    'first_name' => $this->processedBy->first_name,
                    'last_name'  => $this->processedBy->last_name,
                    'email'      => $this->processedBy->email,
                ]),

                'cancelled_by' => $this->whenLoaded('cancelledBy', fn () => $this->cancelledBy ? [
                    'id'         => $this->cancelledBy->id,
                    'full_name'  => trim("{$this->cancelledBy->first_name} {$this->cancelledBy->last_name}"),
                    'first_name' => $this->cancelledBy->first_name,
                    'last_name'  => $this->cancelledBy->last_name,
                    'email'      => $this->cancelledBy->email,
                ] : null),
            ]);
        }

        return $data;
    }
}