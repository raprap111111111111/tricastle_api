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

                // 🎯 Normalize enums, strings, and dates before merging into database
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
        // 1. JLPT Level: Convert 'None', 'N/A', or invalid levels to null
        if (array_key_exists('jlpt_level', $payload)) {
            $jlpt = strtoupper(trim((string) ($payload['jlpt_level'] ?? '')));
            $allowedJlpt = ['N1', 'N2', 'N3', 'N4', 'N5'];
            $payload['jlpt_level'] = in_array($jlpt, $allowedJlpt, true) ? $jlpt : null;
        }

        // 2. Dominant Hand: Map 'ambidextrous' -> 'both'
        if (isset($payload['dominant_hand'])) {
            if ($payload['dominant_hand'] === 'ambidextrous') {
                $payload['dominant_hand'] = 'both';
            } elseif (!in_array($payload['dominant_hand'], ['left', 'right', 'both'], true)) {
                $payload['dominant_hand'] = null;
            }
        }

        // 3. Gender: Ensure valid lowercase enum ['male', 'female']
        if (isset($payload['gender'])) {
            $gender = strtolower((string) $payload['gender']);
            $payload['gender'] = in_array($gender, ['male', 'female'], true) ? $gender : null;
        }

        // 4. Civil Status: Ensure valid enum
        if (isset($payload['civil_status'])) {
            $status  = strtolower((string) $payload['civil_status']);
            $allowed = ['single', 'married', 'widowed', 'separated', 'divorced'];
            $payload['civil_status'] = in_array($status, $allowed, true) ? $status : null;
        }

        // 5. Blood Type: Strip Rh factors (+/-) to safely fit ENUM('A', 'B', 'AB', 'O')
        if (array_key_exists('blood_type', $payload) && $payload['blood_type'] !== null) {
            $bt = strtoupper(trim((string) $payload['blood_type']));
            // Strip '+' and '-' to match standard database ENUM
            $plain = str_replace(['+', '-'], '', $bt);
            $payload['blood_type'] = in_array($plain, ['A', 'B', 'AB', 'O'], true) ? $plain : null;
        }

        // 6. Facebook URL: Clean and cap length at 255 chars
        if (isset($payload['facebook_url']) && is_string($payload['facebook_url'])) {
            $url = trim($payload['facebook_url']);
            $payload['facebook_url'] = $url !== '' ? substr($url, 0, 255) : null;
        }

        // 7. Format dates to 'YYYY-MM-DD' (removes '00:00:00' timestamps)
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