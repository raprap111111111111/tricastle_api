<?php
// app/Domain/Auth/Notifications/WelcomeNotification.php

namespace App\Domain\Auth\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\User;

class WelcomeNotification extends BaseNotification
{
    public function __construct(
        private readonly User $user
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => 'Welcome to TriCastle! 🎉',
            'message'      => "Hi {$this->user->first_name}, your account has been created successfully.",
            'action_url'   => '/dashboard',
            'action_label' => 'Go to Dashboard',
            'meta'         => [
                'user_id' => $this->user->id,
            ],
        ];
    }
}