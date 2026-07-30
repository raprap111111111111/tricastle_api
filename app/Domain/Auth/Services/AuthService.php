<?php

namespace App\Domain\Auth\Services;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function verifyPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function createAccessToken(User $user, string $deviceName = 'api'): string
    {
        return $user->createToken($deviceName)->accessToken;
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function revokeCurrentToken(User $user): void
    {
        $user->token()?->revoke();
    }

    public function recordLoginHistory(User $user, Request $request, string $status = 'success', ?string $failureReason = null): LoginHistory
    {
        return LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $this->detectDeviceType($request->userAgent()),
            'browser' => $this->detectBrowser($request->userAgent()),
            'platform' => $this->detectPlatform($request->userAgent()),
            'status' => $status,
            'failure_reason' => $failureReason,
            'login_method' => 'email',
            'logged_in_at' => now(),
        ]);
    }

    public function updateLastLogin(User $user, string $ip): void
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'login_count' => ($user->login_count ?? 0) + 1,
        ]);
    }

    public function isAccountLocked(User $user): bool
    {
        return $user->locked_until && $user->locked_until->isFuture();
    }

    public function isAccountActive(User $user): bool
    {
        return $user->is_active === true;
    }

    public function incrementFailedAttempts(User $user): void
    {
        $attempts = ($user->failed_login_attempts ?? 0) + 1;
        
        $user->update([
            'failed_login_attempts' => $attempts,
            'locked_until' => $attempts >= 5 ? now()->addMinutes(30) : null,
        ]);
    }

    public function resetFailedAttempts(User $user): void
    {
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    private function detectDeviceType(?string $userAgent): string
    {
        if (!$userAgent) return 'unknown';
        
        if (preg_match('/mobile/i', $userAgent)) return 'mobile';
        if (preg_match('/tablet/i', $userAgent)) return 'tablet';
        return 'desktop';
    }

    private function detectBrowser(?string $userAgent): string
    {
        if (!$userAgent) return 'unknown';
        
        if (preg_match('/chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/safari/i', $userAgent)) return 'Safari';
        if (preg_match('/edge/i', $userAgent)) return 'Edge';
        return 'Other';
    }

    private function detectPlatform(?string $userAgent): string
    {
        if (!$userAgent) return 'unknown';
        
        if (preg_match('/windows/i', $userAgent)) return 'Windows';
        if (preg_match('/mac/i', $userAgent)) return 'macOS';
        if (preg_match('/linux/i', $userAgent)) return 'Linux';
        if (preg_match('/android/i', $userAgent)) return 'Android';
        if (preg_match('/iphone|ipad/i', $userAgent)) return 'iOS';
        return 'Other';
    }
}
