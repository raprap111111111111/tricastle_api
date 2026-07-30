<?php
// app/Policies/NotificationPolicy.php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('notification.viewAny')
            || $user->can('notification.viewOwn');
    }

    public function view(User $user, Notification $notification): bool
    {
        return $user->can('notification.viewAny')
            || ($user->can('notification.viewOwn')
                && $notification->notifiable_id === $user->id
                && $notification->notifiable_type === User::class);
    }

    public function delete(User $user, Notification $notification): bool
    {
        return $user->can('notification.delete')
            || ($notification->notifiable_id === $user->id
                && $notification->notifiable_type === User::class);
    }

    public function markAsRead(User $user, Notification $notification): bool
    {
        return $notification->notifiable_id === $user->id
            && $notification->notifiable_type === User::class;
    }

    public function markAllAsRead(User $user): bool
    {
        return true; 
    }
}