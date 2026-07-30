<?php
// app/Domain/Auth/Notifications/PasswordChangedNotification.php

namespace App\Domain\Auth\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\User;

class PasswordChangedNotification extends BaseNotification
{
    public function __construct(
        private readonly User $user
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => '🔐 Password Changed',
            'message'      => "Hi {$this->user->first_name}, your password was changed. All sessions have been logged out.",
            'action_url'   => '/profile/security',
            'action_label' => 'Review Security',
            'meta'         => [
                'user_id'    => $this->user->id,
                'changed_at' => now()->toISOString(),
            ],
        ];
    }
}