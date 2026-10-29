<?php

namespace App\Domain\ProfileUpdate\Actions;

use App\Models\ApplicantProfileUpdate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ApproveProfileUpdateAction
{
    public function execute(ApplicantProfileUpdate $update, int $staffId): void
    {
        DB::transaction(function () use ($update, $staffId) {
            $applicant = $update->applicant;

            if ($applicant && !empty($update->proposed_payload)) {
                // Remove system/read-only fields if present in proposed payload
                $payload = collect($update->proposed_payload)
                    ->except([
                        'id',
                        'applicant_code',
                        'status',
                        'created_at',
                        'updated_at',
                        'deleted_at'
                    ])
                    ->toArray();

                // 🎯 Normalize enums and dates before merging into database
                $payload = $this->sanitizePayload($payload);

                // Merge proposed changes into applicant table
                $applicant->update($payload);
            }

            // Mark update request as approved
            $update->update([
                'status'      => 'approved',
                'reviewed_by' => $staffId,
                'reviewed_at' => now(),
            ]);
        });
    }

    /**
     * Clean and normalize user-submitted values so they fit database columns and enums.
     */
    protected function sanitizePayload(array $payload): array
    {
        // 1. Dominant Hand: Map 'ambidextrous' -> 'both'
        if (isset($payload['dominant_hand'])) {
            if ($payload['dominant_hand'] === 'ambidextrous') {
                $payload['dominant_hand'] = 'both';
            } elseif (!in_array($payload['dominant_hand'], ['left', 'right', 'both'], true)) {
                $payload['dominant_hand'] = null;
            }
        }

        // 2. Gender: Ensure valid lowercase enum ['male', 'female']
        if (isset($payload['gender'])) {
            $gender = strtolower((string) $payload['gender']);
            $payload['gender'] = in_array($gender, ['male', 'female'], true) ? $gender : null;
        }

        // 3. Civil Status: Ensure valid enum
        if (isset($payload['civil_status'])) {
            $status  = strtolower((string) $payload['civil_status']);
            $allowed = ['single', 'married', 'widowed', 'separated', 'divorced'];
            $payload['civil_status'] = in_array($status, $allowed, true) ? $status : null;
        }

        // 4. Blood Type: Sanitize and validate
        if (isset($payload['blood_type']) && $payload['blood_type'] !== null) {
            $bt = strtoupper(trim((string) $payload['blood_type']));
            $allowed = ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            
            if (!in_array($bt, $allowed, true)) {
                // Fallback: strip Rh factor if database enum has not been migrated yet
                $plain = rtrim($bt, '+-');
                $payload['blood_type'] = in_array($plain, ['A', 'B', 'AB', 'O'], true) ? $plain : null;
            } else {
                $payload['blood_type'] = $bt;
            }
        }

        // 5. Format dates to 'YYYY-MM-DD' (removes '00:00:00' timestamps)
        $dateFields = [
            'date_of_birth',
            'passport_expiry',
            'passport_issue_date',
            'trade_test_date',
            'passport_received_at',
            'passport_expiration_date',
        ];

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $payload)) {
                if (!empty($payload[$dateField])) {
                    try {
                        $payload[$dateField] = Carbon::parse($payload[$dateField])->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $payload[$dateField] = null;
                    }
                } else {
                    $payload[$dateField] = null;
                }
            }
        }

        return $payload;
    }
}