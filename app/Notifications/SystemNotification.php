<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string  $title,
        public string  $message,
        public string  $module = 'system',
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
        public string  $severity = 'info', // info, success, warn, error
        public array   $meta = [],
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'        => $this->title,
            'message'      => $this->message,
            'module'       => $this->module,
            'action_url'   => $this->actionUrl,
            'action_label' => $this->actionLabel,
            'severity'     => $this->severity,
            'meta'         => $this->meta,
        ];
    }
}