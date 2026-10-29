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
                // Remove system/read-only fields from payload
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

                // Normalize enums, strings, and dates
                $payload = $this->sanitizePayload($payload);

                // Update applicant record
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
     * Clean and normalize user-submitted values so they safely fit database columns.
     */
    protected function sanitizePayload(array $payload): array
    {
        $trimStr = function ($val, int $max = 255): ?string {
            if ($val === null || $val === '') return null;
            $str = trim((string) $val);
            return $str !== '' ? mb_substr($str, 0, $max) : null;
        };

        // 1. Strings & URLs max character capping
        $stringLimits = [
            'mobile'                         => 50,
            'phone'                          => 50,
            'facebook_url'                  => 255,
            'nationality'                    => 100,
            'religion'                       => 50,
            'birthplace'                     => 150,
            'current_address'                => 255,
            'permanent_address'              => 255,
            'city'                           => 100,
            'province'                       => 100,
            'postal_code'                    => 20,
            'passport_number'                => 50,
            'sss_number'                     => 50,
            'tin_number'                     => 50,
            'philhealth_number'              => 50,
            'pagibig_number'                 => 50,
            'trade_or_occupation'            => 100,
            'skill_category'                 => 100,
            'titp_occupation'                => 100,
            'father_name'                    => 150,
            'mother_name'                    => 150,
            'spouse_name'                    => 150,
            'emergency_contact_name'        => 150,
            'emergency_contact_relationship' => 100,
            'emergency_contact_phone'       => 50,
            'emergency_contact_address'     => 255,
        ];

        foreach ($stringLimits as $field => $maxLen) {
            if (array_key_exists($field, $payload)) {
                $payload[$field] = $trimStr($payload[$field], $maxLen);
            }
        }

        // 2. Numbers
        if (array_key_exists('number_of_children', $payload)) {
            $payload['number_of_children'] = is_numeric($payload['number_of_children']) ? (int) $payload['number_of_children'] : 0;
        }
        if (array_key_exists('years_japan_experience', $payload)) {
            $payload['years_japan_experience'] = is_numeric($payload['years_japan_experience']) ? (int) $payload['years_japan_experience'] : null;
        }
        if (array_key_exists('height_cm', $payload)) {
            $payload['height_cm'] = is_numeric($payload['height_cm']) ? (float) $payload['height_cm'] : null;
        }
        if (array_key_exists('weight_kg', $payload)) {
            $payload['weight_kg'] = is_numeric($payload['weight_kg']) ? (float) $payload['weight_kg'] : null;
        }

        // 3. Booleans
        $boolFields = [
            'understands_basic_english',
            'willing_to_be_deployed',
            'japan_deployment_ready',
            'previous_japan_experience',
            'has_titp_certificate',
            'ssw_eligible',
        ];
        foreach ($boolFields as $bField) {
            if (array_key_exists($bField, $payload)) {
                $payload[$bField] = filter_var($payload[$bField], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }

        // 4. JLPT Level ('None', 'N/A' -> null)
        if (array_key_exists('jlpt_level', $payload)) {
            $jlpt = strtoupper(trim((string) ($payload['jlpt_level'] ?? '')));
            $allowedJlpt = ['N1', 'N2', 'N3', 'N4', 'N5'];
            $payload['jlpt_level'] = in_array($jlpt, $allowedJlpt, true) ? $jlpt : null;
        }

        // 5. Dominant Hand ('ambidextrous' -> 'both')
        if (isset($payload['dominant_hand'])) {
            $hand = strtolower(trim((string) $payload['dominant_hand']));
            if ($hand === 'ambidextrous') {
                $payload['dominant_hand'] = 'both';
            } else {
                $payload['dominant_hand'] = in_array($hand, ['left', 'right', 'both'], true) ? $hand : null;
            }
        }

        // 6. Gender
        if (isset($payload['gender'])) {
            $gender = strtolower(trim((string) $payload['gender']));
            $payload['gender'] = in_array($gender, ['male', 'female'], true) ? $gender : null;
        }

        // 7. Civil Status
        if (isset($payload['civil_status'])) {
            $status  = strtolower(trim((string) $payload['civil_status']));
            $allowed = ['single', 'married', 'widowed', 'separated', 'divorced'];
            $payload['civil_status'] = in_array($status, $allowed, true) ? $status : null;
        }

        // 8. Blood Type (Supports 'A', 'B', 'AB', 'O', 'A+', 'A-', etc.)
        if (array_key_exists('blood_type', $payload) && $payload['blood_type'] !== null) {
            $bt = strtoupper(trim((string) $payload['blood_type']));
            $payload['blood_type'] = $bt !== '' ? mb_substr($bt, 0, 10) : null;
        }

        // 9. Format Dates to 'YYYY-MM-DD'
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