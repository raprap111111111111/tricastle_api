<?php

namespace App\Domain\Applicant\Actions;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcceptSelfRegisteredApplicantAction
{
    public function execute(Applicant $applicant, int $staffUserId): Applicant
    {
        return DB::transaction(function () use ($applicant, $staffUserId) {
            // 1. Move status to 'pending' (enters regular recruitment pipeline)
            $applicant->update([
                'status'      => ApplicantStatus::Pending,
                'reviewed_by' => $staffUserId,
            ]);

            // 2. Activate portal account — skipped safely if feature not wired up yet
            $this->activatePortalAccount($applicant);

            // NOTE: do NOT eager-load 'portalAccount' here unless the relation exists
            return $applicant->fresh(['reviewer']);
        });
    }

    /**
     * Activate the applicant's portal login if a portalAccount() relation
     * is defined on the model and a record exists. Never blocks acceptance.
     */
    private function activatePortalAccount(Applicant $applicant): void
    {
        if (!method_exists($applicant, 'portalAccount')) {
            return; // No portal account feature yet — nothing to do
        }

        try {
            $account = $applicant->portalAccount;

            if ($account) {
                $account->update(['status' => 'active']);
            }
        } catch (RelationNotFoundException $e) {
            Log::warning('Portal account activation skipped (relation missing).', [
                'applicant_id' => $applicant->id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Portal account activation failed: ' . $e->getMessage(), [
                'applicant_id' => $applicant->id,
            ]);
        }
    }
}