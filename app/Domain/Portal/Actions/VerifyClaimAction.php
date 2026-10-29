<?php
namespace App\Domain\Portal\Actions;

use App\Models\Applicant;
use App\Models\ApplicantPortalAccount;
use App\Domain\Portal\DTOs\VerifyClaimDTO;

class VerifyClaimAction {
    public function execute(VerifyClaimDTO $dto): ?array {
        $applicant = Applicant::whereRaw('LOWER(TRIM(first_name)) = ?', [strtolower(trim($dto->firstName))])
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [strtolower(trim($dto->lastName))])
            ->whereDate('date_of_birth', $dto->dateOfBirth)
            ->first();

        if (!$applicant) return null;

        $hasAccount = ApplicantPortalAccount::where('applicant_id', $applicant->id)->exists();

        return [
            'applicant_id' => $applicant->id,
            'has_account' => $hasAccount
        ];
    }
}
