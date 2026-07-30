<?php
// app/Domain/DocumentExpiryAlert/Actions/CheckExpiringDocumentsAction.php

namespace App\Domain\DocumentExpiryAlert\Actions;

use App\Domain\DocumentExpiryAlert\Notifications\DocumentExpiredNotification;
use App\Domain\DocumentExpiryAlert\Notifications\DocumentExpiringNotification;
use App\Domain\DocumentExpiryAlert\Repositories\DocumentExpiryAlertRepository;
use App\Models\DocumentExpiryAlert;
use App\Models\User;

class CheckExpiringDocumentsAction
{
    public function __construct(
        private readonly DocumentExpiryAlertRepository $repository
    ) {}

    public function execute(): int
    {
        $alerts = $this->repository->getPendingNotifications();

        $count = 0;

        foreach ($alerts as $alert) {
            // ─── Choose notification based on alert type ───────
            $notification = match($alert->alert_type) {
                'expired'                        => new DocumentExpiredNotification($alert),
                '30_days', '60_days', '90_days'  => new DocumentExpiringNotification($alert),
            };

            // ─── Notify users who can view documents ───────────
            User::permission('applicant-document.viewAny')
                ->get()
                ->each(fn(User $user) => $user->notify($notification));

            // ─── Mark notification as sent ─────────────────────
            $this->repository->update($alert->id, [
                'notification_sent'    => true,
                'notification_sent_at' => now(),
            ]);

            $count++;
        }

        return $count;
    }
}