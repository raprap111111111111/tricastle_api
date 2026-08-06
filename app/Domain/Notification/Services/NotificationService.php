<?php

namespace App\Domain\Notification\Services;

use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\PubNubService;

/**
 * 🔔 Notification Service
 * 
 * ⚠️ IMPORTANT: Notifications are STAFF-ONLY.
 * Applicants do NOT have accounts and never receive notifications.
 * All notifications target internal users (staff) based on permissions.
 */
class NotificationService
{
    public function __construct(
        private readonly PubNubService $pubnub,
    ) {}

    /**
     * Send notification to a single STAFF user
     */
    public function sendToUser(
        User    $user,
        string  $title,
        string  $message,
        string  $module = 'system',
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        string  $severity = 'info',
        array   $meta = [],
    ): void {
        // ✅ Safety: don't send to inactive users
        if (!$user->is_active) {
            return;
        }

        // 1️⃣ Save to `notifications` table
        $user->notify(new SystemNotification(
            title:       $title,
            message:     $message,
            module:      $module,
            actionUrl:   $actionUrl,
            actionLabel: $actionLabel,
            severity:    $severity,
            meta:        $meta,
        ));

        // 2️⃣ Push real-time via PubNub to user's personal channel
        $latest = $user->notifications()->latest()->first();
        if (!$latest) return;

        $this->pubnub->publish("tricastle.user.{$user->id}", [
            'event'   => 'notification.created',
            'payload' => [
                'id'         => $latest->id,
                'type'       => $latest->type,
                'data'       => $latest->data,
                'read_at'    => null,
                'created_at' => $latest->created_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Send to multiple STAFF users
     */
    public function sendToUsers(
        iterable $users,
        string   $title,
        string   $message,
        string   $module = 'system',
        ?string  $actionUrl = null,
        ?string  $actionLabel = null,
        string   $severity = 'info',
        array    $meta = [],
    ): void {
        foreach ($users as $user) {
            $this->sendToUser(
                $user, $title, $message, $module,
                $actionUrl, $actionLabel, $severity, $meta,
            );
        }
    }

    /**
     * 🎯 Send to all STAFF users who have a SPECIFIC PERMISSION
     * 
     * Only ACTIVE staff members are notified.
     *
     * Usage:
     *   sendToPermission('applicant.viewAny', ...)
     *   sendToPermission(['applicant.viewAny', 'applicant.approve'], ...)
     */
    public function sendToPermission(
        string|array $permissions,
        string       $title,
        string       $message,
        string       $module = 'system',
        ?string      $actionUrl = null,
        ?string      $actionLabel = null,
        string       $severity = 'info',
        array        $meta = [],
        ?int         $excludeUserId = null,
    ): void {
        $permissions = is_array($permissions) ? $permissions : [$permissions];

        // Get all ACTIVE staff who have ANY of the given permissions
        // (either directly assigned OR through a role)
        $staff = User::query()
            ->where('is_active', true)                    // ✅ Active staff only
            ->where(function ($query) use ($permissions) {
                $query
                    ->whereHas('permissions', fn ($q) => $q->whereIn('name', $permissions))
                    ->orWhereHas('roles.permissions', fn ($q) => $q->whereIn('name', $permissions));
            })
            ->when($excludeUserId, fn ($q) => $q->where('id', '!=', $excludeUserId))
            ->get()
            ->unique('id');

        $this->sendToUsers(
            $staff, $title, $message, $module,
            $actionUrl, $actionLabel, $severity, $meta,
        );
    }

    /**
     * Send to all ACTIVE staff (broadcast)
     * Use sparingly — only for system-wide announcements
     */
    public function broadcast(
        string  $title,
        string  $message,
        string  $module = 'system',
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        string  $severity = 'info',
        array   $meta = [],
        ?int    $excludeUserId = null,
    ): void {
        $staff = User::where('is_active', true)
            ->when($excludeUserId, fn ($q) => $q->where('id', '!=', $excludeUserId))
            ->get();

        $this->sendToUsers(
            $staff, $title, $message, $module,
            $actionUrl, $actionLabel, $severity, $meta,
        );

        // Also broadcast on global channel
        $this->pubnub->publish('tricastle.notifications', [
            'event'   => 'notification.broadcast',
            'payload' => [
                'title'   => $title,
                'message' => $message,
                'module'  => $module,
            ],
        ]);
    }
}