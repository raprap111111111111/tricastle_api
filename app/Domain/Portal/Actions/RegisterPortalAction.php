<?php

namespace App\Domain\Portal\Actions;

use App\Domain\Portal\DTOs\VerifyClaimDTO;
use App\Models\Applicant;
use App\Models\ApplicantPortalAccount;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterPortalAction
{
    public function __construct(private VerifyClaimAction $verifyClaimAction) {}

    public function execute(array $data): array
    {
        // 1. Instantiate the DTO expected by VerifyClaimAction
        $dto = new VerifyClaimDTO(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            dateOfBirth: $data['date_of_birth']
        );

        // 2. Re-verify identity on the server
        $verification = $this->verifyClaimAction->execute($dto);

        if (!$verification) {
            throw new Exception("Identity verification failed. No matching applicant record found.");
        }

        if ($verification['has_account']) {
            throw new Exception("An account already exists for this applicant record.");
        }

        // 3. Perform atomic registration
        return DB::transaction(function () use ($data, $verification) {
            $user = User::create([
                'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Bind applicant record to user account
            $applicant = Applicant::find($verification['applicant_id']);
            if ($applicant) {
                $applicant->user_id = $user->id;
                $applicant->save();
            }

            // Create portal account record if table exists
            if (class_exists(ApplicantPortalAccount::class)) {
                ApplicantPortalAccount::firstOrCreate(
                    ['applicant_id' => $verification['applicant_id']],
                    [
                        'user_id' => $user->id,
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                    ]
                );
            }

            return [
                'token' => $user->createToken('portal-token')->plainTextToken,
                'user' => $user,
            ];
        });
    }
}