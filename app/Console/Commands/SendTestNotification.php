<?php

namespace App\Console\Commands;

use App\Domain\Notification\Services\NotificationService;
use App\Models\User;
use Illuminate\Console\Command;

class SendTestNotification extends Command
{
    protected $signature = 'notify:test {email?} {--count=5}';
    protected $description = 'Send test notifications to a user';

    public function handle(NotificationService $service): int
    {
        $email = $this->argument('email');
        $count = (int) $this->option('count');

        $user = $email
            ? User::where('email', $email)->first()
            : User::first();

        if (!$user) {
            $this->error("❌ User not found");
            return 1;
        }

        $this->info("📤 Sending {$count} notification(s) to: {$user->email} (ID: {$user->id})");

        $notifications = [
            ['🎉 Welcome to Tricastle!', 'Your notification system is live!', 'system', '/dashboard', 'success'],
            ['👤 New Applicant Registered', 'Juan Dela Cruz was registered.', 'applicant', '/applicants', 'info'],
            ['📄 Document Needs Review', 'Passport needs verification.', 'document', '/documents', 'warn'],
            ['🎓 New Batch Created', 'Batch #50 is ready.', 'batch', '/batches', 'success'],
            ['❌ Document Rejected', 'Passport needs re-upload.', 'document', '/documents', 'error'],
        ];

        for ($i = 0; $i < $count; $i++) {
            $n = $notifications[$i % count($notifications)];

            $service->sendToUser(
                user:        $user,
                title:       $n[0],
                message:     $n[1],
                module:      $n[2],
                actionUrl:   $n[3],
                actionLabel: 'View',
                severity:    $n[4],
            );

            $this->line("  ✅ Sent: {$n[0]}");
        }

        $this->info("🎉 Done! Refresh your browser now.");
        return 0;
    }
}