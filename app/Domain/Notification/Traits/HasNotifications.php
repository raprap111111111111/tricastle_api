<?php

namespace App\Domain\Notification\Traits;

use App\Domain\Notification\Services\NotificationService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * 🔔 HasNotifications Trait
 * 
 * Drop this into ANY action to get simple notification methods.
 * 
 * Usage:
 *   class YourAction {
 *       use HasNotifications;
 *       
 *       public function execute(...) {
 *           $this->notify(
 *               title: '✅ Something happened',
 *               message: 'Details here...',
 *               permissions: 'applicant.viewAny',
 *           );
 *       }
 *   }
 */
trait HasNotifications
{
    /**
     * Lazily resolve the notification service (no constructor injection needed!)
     */
    private function notificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    // ═══════════════════════════════════════════════════════════
    // 🎯 THE ONE METHOD YOU'LL USE MOST
    // ═══════════════════════════════════════════════════════════

    /**
     * 🔔 Send notification — the ONE method you'll use everywhere.
     * 
     * @param string       $title        Notification title (e.g. '✅ Applicant Approved')
     * @param string       $message      Body message
     * @param string|array $permissions  Permission(s) — sends to staff who have it
     *                                    Pass null to send to specific user only
     * @param User|int|null $user         Optional: send to specific user (email or User instance)
     * @param string       $module       Module name for filtering (applicant, document, batch, etc.)
     * @param string|null  $actionUrl    Optional URL to navigate to
     * @param string|null  $actionLabel  Optional button label
     * @param string       $severity     info | success | warn | error
     * @param array        $meta         Extra metadata
     * @param int|null     $excludeUserId User to exclude (defaults to current auth user)
     */
    protected function notify(
        string            $title,
        string            $message,
        string|array|null $permissions = null,
        User|int|null     $user = null,
        string            $module = 'system',
        ?string           $actionUrl = null,
        ?string           $actionLabel = null,
        string            $severity = 'info',
        array             $meta = [],
        ?int              $excludeUserId = null,
    ): void {
        // Auto-exclude the current authenticated user (unless overridden)
        $excludeUserId ??= Auth::id();

        // ─── Option 1: Send to a SPECIFIC USER ─────────────
        if ($user !== null) {
            $userModel = $user instanceof User ? $user : User::find($user);

            if (!$userModel || !$userModel->is_active) return;
            if ($userModel->id === $excludeUserId) return;

            $this->notificationService()->sendToUser(
                user:        $userModel,
                title:       $title,
                message:     $message,
                module:      $module,
                actionUrl:   $actionUrl,
                actionLabel: $actionLabel,
                severity:    $severity,
                meta:        $meta,
            );
            return;
        }

        // ─── Option 2: Send to STAFF WITH PERMISSION ───────
        if ($permissions !== null) {
            $this->notificationService()->sendToPermission(
                permissions:   $permissions,
                title:         $title,
                message:       $message,
                module:        $module,
                actionUrl:     $actionUrl,
                actionLabel:   $actionLabel,
                severity:      $severity,
                meta:          $meta,
                excludeUserId: $excludeUserId,
            );
            return;
        }
    }

    // ═══════════════════════════════════════════════════════════
    // 🎨 SHORTCUT METHODS (optional convenience)
    // ═══════════════════════════════════════════════════════════

    /** 🎯 Send to staff with permission */
    protected function notifyStaff(
        string|array $permissions,
        string       $title,
        string       $message,
        string       $module = 'system',
        ?string      $actionUrl = null,
        string       $severity = 'info',
        array        $meta = [],
    ): void {
        $this->notify(
            title:       $title,
            message:     $message,
            permissions: $permissions,
            module:      $module,
            actionUrl:   $actionUrl,
            actionLabel: $actionUrl ? 'View' : null,
            severity:    $severity,
            meta:        $meta,
        );
    }

    /** 🎯 Send to a specific user */
    protected function notifyUser(
        User|int|null $user,
        string        $title,
        string        $message,
        string        $module = 'system',
        ?string       $actionUrl = null,
        string        $severity = 'info',
        array         $meta = [],
    ): void {
        if (!$user) return;

        $this->notify(
            title:       $title,
            message:     $message,
            user:        $user,
            module:      $module,
            actionUrl:   $actionUrl,
            actionLabel: $actionUrl ? 'View' : null,
            severity:    $severity,
            meta:        $meta,
        );
    }

    /** 🎉 Success notification (shorthand) */
    protected function notifySuccess(
        string|array $permissions,
        string       $title,
        string       $message,
        string       $module = 'system',
        ?string      $actionUrl = null,
    ): void {
        $this->notifyStaff($permissions, $title, $message, $module, $actionUrl, 'success');
    }

    /** ⚠️ Warning notification (shorthand) */
    protected function notifyWarning(
        string|array $permissions,
        string       $title,
        string       $message,
        string       $module = 'system',
        ?string      $actionUrl = null,
    ): void {
        $this->notifyStaff($permissions, $title, $message, $module, $actionUrl, 'warn');
    }

    /** ❌ Error notification (shorthand) */
    protected function notifyError(
        string|array $permissions,
        string       $title,
        string       $message,
        string       $module = 'system',
        ?string      $actionUrl = null,
    ): void {
        $this->notifyStaff($permissions, $title, $message, $module, $actionUrl, 'error');
    }

    /** 📢 Broadcast to ALL active staff */
    protected function notifyAll(
        string  $title,
        string  $message,
        string  $module = 'system',
        ?string $actionUrl = null,
        string  $severity = 'info',
    ): void {
        $this->notificationService()->broadcast(
            title:         $title,
            message:       $message,
            module:        $module,
            actionUrl:     $actionUrl,
            actionLabel:   $actionUrl ? 'View' : null,
            severity:      $severity,
            excludeUserId: Auth::id(),
        );
    }
}