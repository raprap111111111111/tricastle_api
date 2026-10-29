<?php

namespace App\Domain\Portal\Actions;

use App\Domain\Portal\DTOs\VerifyClaimDTO;
use App\Models\Applicant;
use App\Models\ApplicantPortalAccount;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class VerifyClaimAction
{
    public function execute(VerifyClaimDTO $dto): ?array
    {
        // 1. Collect all name inputs & date of birth
        $inputs = array_filter([
            $dto->firstName ?? '',
            $dto->middleName ?? '',
            $dto->lastName ?? '',
            $dto->dateOfBirth ?? '',
        ]);

        $dobInput = null;
        $nameTokens = [];

        foreach ($inputs as $input) {
            $str = trim((string) $input);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $str) || preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $str)) {
                $dobInput = str_replace('/', '-', $str);
            } else if ($str !== '') {
                $words = preg_split('/\s+/', strtolower($str));
                foreach ($words as $w) {
                    $clean = preg_replace('/[^a-z0-9]/', '', $w);
                    if ($clean !== '') {
                        $nameTokens[] = $clean;
                    }
                }
            }
        }

        if (!$dobInput && !empty($dto->dateOfBirth)) {
            try {
                $dobInput = Carbon::parse($dto->dateOfBirth)->format('Y-m-d');
            } catch (\Throwable $e) {
                $dobInput = substr((string) $dto->dateOfBirth, 0, 10);
            }
        }

        if (empty($nameTokens) || empty($dobInput)) {
            return null;
        }

        // 2. Fetch candidates matching DOB or Name tokens (ignoring global status scopes)
        $query = Applicant::query()->withoutGlobalScopes();

        $query->where(function ($q) use ($dobInput, $nameTokens) {
            $q->whereDate('date_of_birth', $dobInput)
              ->orWhere('date_of_birth', 'LIKE', "{$dobInput}%");

            foreach ($nameTokens as $token) {
                if (strlen($token) >= 3) {
                    $q->orWhereRaw("LOWER(last_name) LIKE ?", ["%{$token}%"])
                      ->orWhereRaw("LOWER(first_name) LIKE ?", ["%{$token}%"]);
                }
            }
        });

        $candidates = $query->get();

        if ($candidates->isEmpty()) {
            $candidates = Applicant::query()->withoutGlobalScopes()->get();
        }

        // 3. Score candidates by matching all name tokens
        $matchedApplicant = null;

        foreach ($candidates as $applicant) {
            // Check DOB
            $dbDob = null;
            if ($applicant->date_of_birth) {
                try {
                    $dbDob = Carbon::parse($applicant->date_of_birth)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $dbDob = substr((string) $applicant->date_of_birth, 0, 10);
                }
            }

            if ($dbDob !== $dobInput) {
                continue;
            }

            // Combine all DB names into one master string
            $dbFullName = Str::of(
                ($applicant->first_name ?? '') . ' ' .
                ($applicant->middle_name ?? '') . ' ' .
                ($applicant->last_name ?? '')
            )
            ->lower()
            ->replaceMatches('/[^a-z0-9]/', '')
            ->toString();

            // Check if EVERY token typed by user exists in dbFullName
            $allTokensMatch = true;
            foreach ($nameTokens as $token) {
                if (!str_contains($dbFullName, $token)) {
                    $allTokensMatch = false;
                    break;
                }
            }

            if ($allTokensMatch) {
                $matchedApplicant = $applicant;
                break;
            }
        }

        if (!$matchedApplicant) {
            return null;
        }

        $hasAccount = !is_null($matchedApplicant->user_id) || ApplicantPortalAccount::where('applicant_id', $matchedApplicant->id)->exists();

        return [
            'applicant_id' => $matchedApplicant->id,
            'has_account'  => $hasAccount,
        ];
    }
}