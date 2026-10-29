<?php
namespace App\Domain\ProfileUpdate\Actions;

use App\Models\ApplicantProfileUpdate;

class DeclineProfileUpdateAction {
    public function execute(ApplicantProfileUpdate $update, string $reason, int $staffId): void {
        $update->update([
            'status' => 'declined',
            'decline_reason' => $reason,
            'reviewed_by' => $staffId,
            'reviewed_at' => now()
        ]);
    }
}
