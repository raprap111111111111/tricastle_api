<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\ActivityLog\Traits\LogsActivity;
use App\Models\Applicant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UpdateApplicantRemarksAction
{
    use LogsActivity;

    public function execute(Applicant $applicant, ?array $remarks): Applicant
    {
        return DB::transaction(function () use ($applicant, $remarks) {
            $cleanRemarks = !empty($remarks) ? array_values($remarks) : null;

            $applicant->update([
                'remarks' => $cleanRemarks,
            ]);

            Log::info('Applicant remarks updated', [
                'applicant_id'  => $applicant->id,
                'remarks_count' => count($cleanRemarks ?? []),
                'updated_by' => Auth::id(),
            ]);

            return $applicant->fresh();
        });
    }
}