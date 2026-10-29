<?php

namespace App\Http\Controllers\v1\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ApplicantLoginLog;
use App\Models\ApplicantPortalAccount;
use App\Http\Resources\v1\ApplicantLoginLogResource;

class ApplicantLoginLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ApplicantLoginLog::with([
                'account.applicant:id,first_name,middle_name,last_name,applicant_code',
            ])
            ->when($request->query('email'), fn ($q, $e) => $q->where('email', 'like', "%{$e}%"))
            ->when(in_array($request->query('status'), ['success', 'failed']),
                fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->boolean('unhashed_only'),
                fn ($q) => $q->where('password_was_hashed', false))
            ->latest('attempted_at')
            ->paginate($request->integer('per_page', 20));

        return ApplicantLoginLogResource::collection($logs);
    }

    /**
     * One-off audit: which portal accounts currently store an UNHASHED password?
     */
    public function unhashedAudit()
    {
        $accounts = ApplicantPortalAccount::with('applicant:id,first_name,last_name,applicant_code')->get();

        $unhashed = $accounts
            ->filter(fn (ApplicantPortalAccount $a) => !Hash::isHashed($a->password))
            ->map(fn (ApplicantPortalAccount $a) => [
                'id'             => $a->id,
                'email'          => $a->email,
                'applicant_code' => $a->applicant?->applicant_code,
                'applicant_name' => trim(($a->applicant?->first_name ?? '') . ' ' . ($a->applicant?->last_name ?? '')),
            ])
            ->values();

        return response()->json([
            'total_accounts' => $accounts->count(),
            'unhashed_count' => $unhashed->count(),
            'unhashed'       => $unhashed,
        ]);
    }
}