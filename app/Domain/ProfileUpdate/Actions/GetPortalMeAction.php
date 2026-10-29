<?php

namespace App\Domain\ProfileUpdate\Actions;

use App\Models\ApplicantProfileUpdate;
use App\Models\ApplicantPortalAccount;

class GetPortalMeAction
{
    protected array $requiredFields = [
        'current_address',
        'city',
        'province',
        'mobile',
        'birthplace',
        'civil_status',
        'religion',
        'passport_number',
        'passport_expiry',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'date_of_birth',
        'gender',
        'nationality',
        'facebook_url',
    ];

    public function execute(ApplicantPortalAccount $account): array
    {
        $account->loadMissing('applicant');
        $applicant = $account->applicant;

        $applicantData = null;
        $missingFields = [];
        $pendingRequest = null;

        if ($applicant) {
            // Auto-replace placeholder .legacy email with the portal login email
            if (!empty($account->email) && (empty($applicant->email) || str_ends_with($applicant->email, '.legacy'))) {
                $applicant->update(['email' => $account->email]);
                $applicant->email = $account->email;
            }

            $applicantData = [
                'id' => $applicant->id,
                'first_name' => $applicant->first_name,
                'middle_name' => $applicant->middle_name,
                'last_name' => $applicant->last_name,
                'applicant_code' => $applicant->applicant_code,
                'email' => $applicant->email,
                'mobile' => $applicant->mobile,
                'phone' => $applicant->phone,
                'facebook_url' => $applicant->facebook_url,
                'gender' => $applicant->gender,
                'civil_status' => $applicant->civil_status,
                'date_of_birth' => $applicant->date_of_birth ? \Carbon\Carbon::parse($applicant->date_of_birth)->format('Y-m-d') : null,
                'number_of_children' => $applicant->number_of_children,
                'nationality' => $applicant->nationality,

                // Address & Physical
                'height_cm' => $applicant->height_cm,
                'weight_kg' => $applicant->weight_kg,
                'dominant_hand' => $applicant->dominant_hand,
                'blood_type' => $applicant->blood_type,
                'province' => $applicant->province,
                'city' => $applicant->city,
                'current_address' => $applicant->current_address,
                'permanent_address' => $applicant->permanent_address,
                'postal_code' => $applicant->postal_code,
                'birthplace' => $applicant->birthplace,
                'religion' => $applicant->religion,

                // Documents & IDs
                'passport_number' => $applicant->passport_number,
                'passport_expiry' => $applicant->passport_expiry ? \Carbon\Carbon::parse($applicant->passport_expiry)->format('Y-m-d') : null,
                'passport_issue_date' => $applicant->passport_issue_date ? \Carbon\Carbon::parse($applicant->passport_issue_date)->format('Y-m-d') : null,
                'sss_number' => $applicant->sss_number,
                'tin_number' => $applicant->tin_number,
                'philhealth_number' => $applicant->philhealth_number,
                'pagibig_number' => $applicant->pagibig_number,

                // Deployment & Skills
                'trade_or_occupation' => $applicant->trade_or_occupation,
                'skill_category' => $applicant->skill_category,
                'jlpt_level' => $applicant->jlpt_level,
                'understands_basic_english' => $applicant->understands_basic_english,
                'willing_to_be_deployed' => $applicant->willing_to_be_deployed,
                'japan_deployment_ready' => $applicant->japan_deployment_ready,
                'previous_japan_experience' => $applicant->previous_japan_experience,
                'years_japan_experience' => $applicant->years_japan_experience,
                'has_titp_certificate' => $applicant->has_titp_certificate,
                'titp_occupation' => $applicant->titp_occupation,
                'ssw_eligible' => $applicant->ssw_eligible,

                // Family & Emergency
                'father_name' => $applicant->father_name,
                'mother_name' => $applicant->mother_name,
                'spouse_name' => $applicant->spouse_name,
                'emergency_contact_name' => $applicant->emergency_contact_name,
                'emergency_contact_relationship' => $applicant->emergency_contact_relationship,
                'emergency_contact_phone' => $applicant->emergency_contact_phone,
                'emergency_contact_address' => $applicant->emergency_contact_address,
            ];

            $missingFields = collect($this->requiredFields)->filter(function ($field) use ($applicant) {
                return empty($applicant->{$field});
            })->values()->all();

            $pending = ApplicantProfileUpdate::where('applicant_id', $applicant->id)
                ->where('status', 'pending')
                ->first();

            if ($pending) {
                $pendingRequest = [
                    'id' => $pending->id,
                    'proposed_payload' => $pending->proposed_payload,
                    'status' => $pending->status,
                    'created_at' => $pending->created_at?->toIso8601String(),
                ];
            }
        }

        return [
            'account' => [
                'id' => $account->id,
                'email' => $account->email,
                'applicant_id' => $account->applicant_id,
                'status' => $account->status,
                'last_login_at' => $account->last_login_at?->toIso8601String(),
            ],
            'applicant' => $applicantData,
            'missing_fields' => $missingFields,
            'pending_request' => $pendingRequest,
        ];
    }
}
