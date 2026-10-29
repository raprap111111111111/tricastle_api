<?php

namespace App\Http\Controllers\v1\Portal;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\ApplicantLoginLog;
use App\Models\ApplicantPortalAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PortalAuthController extends Controller
{
    private function passwordIsHashed(?string $password): bool
    {
        if (!$password) {
            return false;
        }

        try {
            return Hash::isHashed($password);
        } catch (\Throwable) {
            return str_starts_with($password, '$2y$');
        }
    }

    private function logAttempt(
        ?ApplicantPortalAccount $account,
        string $email,
        string $status,
        Request $request,
        bool $passwordWasHashed,
    ): void {
        ApplicantLoginLog::create([
            'applicant_portal_account_id' => $account?->id,
            'email'               => $email,
            'status'              => $status,
            'ip_address'          => $request->ip(),
            'user_agent'          => substr((string) $request->userAgent(), 0, 500),
            'password_was_hashed' => $passwordWasHashed,
            'attempted_at'        => now(),
        ]);
    }

    /**
     * POST /api/v1/portal/verify-claim
     */
    public function verify(Request $request)
    {
        $data = $request->validate([
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date'],
        ]);

        $applicant = Applicant::query()
            ->whereRaw('LOWER(TRIM(first_name)) = ?', [mb_strtolower(trim($data['first_name']))])
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [mb_strtolower(trim($data['last_name']))])
            ->whereDate('date_of_birth', $data['date_of_birth'])
            ->first();

        if (!$applicant) {
            return response()->json([
                'verified' => false,
                'message'  => 'Record not found. Ensure spelling and date of birth match your registration record.',
            ], 404);
        }

        $hasAccount = ApplicantPortalAccount::where('applicant_id', $applicant->id)->exists();

        return response()->json([
            'verified'     => true,
            'applicant_id' => $applicant->id,
            'has_account'  => $hasAccount,
        ]);
    }

    /**
     * POST /api/v1/portal/register
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name'            => ['required', 'string', 'max:100'],
            'last_name'             => ['required', 'string', 'max:100'],
            'date_of_birth'         => ['required', 'date'],
            'email'                 => ['required', 'email', 'unique:applicant_portal_accounts,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $applicant = Applicant::query()
            ->whereRaw('LOWER(TRIM(first_name)) = ?', [mb_strtolower(trim($data['first_name']))])
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [mb_strtolower(trim($data['last_name']))])
            ->whereDate('date_of_birth', $data['date_of_birth'])
            ->first();

        if (!$applicant) {
            throw ValidationException::withMessages([
                'general' => 'Identity verification failed. Information does not match database record.',
            ]);
        }

        if (ApplicantPortalAccount::where('applicant_id', $applicant->id)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'An account already exists for this applicant. Please log in instead.',
            ]);
        }

        $account = ApplicantPortalAccount::create([
            'applicant_id' => $applicant->id,
            'email'        => $data['email'],
            'password'     => $data['password'], // hashed cast on model
            'status'       => 'active',
        ]);

        if (empty($applicant->email)) {
            $applicant->update(['email' => $data['email']]);
        }

        $token = $account->createToken('portal')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful.',
            'token'   => $token,
            'account' => [
                'id'    => $account->id,
                'email' => $account->email,
            ],
        ], 201);
    }

    /**
     * POST /api/v1/portal/login
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $account = ApplicantPortalAccount::with('applicant')
            ->where('email', $data['email'])
            ->first();

        $storedIsHashed = $this->passwordIsHashed($account?->password);

        if ($account && !$storedIsHashed && hash_equals((string) $account->password, $data['password'])) {
            $account->update(['password' => $data['password']]);
            $storedIsHashed = true;
        }

        $isValid = $account
            && $storedIsHashed
            && Hash::check($data['password'], $account->password)
            && $account->status === 'active';

        if (!$isValid) {
            $this->logAttempt($account, $data['email'], 'failed', $request, $storedIsHashed);

            return response()->json([
                'message' => $account && $account->status !== 'active'
                    ? 'Your account has been disabled. Please contact staff.'
                    : 'Invalid email or password.',
            ], 401);
        }

        $this->logAttempt($account, $data['email'], 'success', $request, $storedIsHashed);

        $account->update(['last_login_at' => now()]);
        $token = $account->createToken('portal')->plainTextToken;

        return response()->json([
            'token'   => $token,
            'account' => [
                'id'        => $account->id,
                'email'     => $account->email,
                'applicant' => $account->applicant?->only([
                    'id', 'applicant_code', 'first_name', 'middle_name', 'last_name', 'email',
                ]),
            ],
        ]);
    }

    /**
     * POST /api/v1/portal/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}