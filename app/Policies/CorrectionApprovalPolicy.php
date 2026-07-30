<?php

namespace App\Policies;

use App\Models\CorrectionApproval;
use App\Models\User;

class CorrectionApprovalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('correction-approval.viewAny');
    }

    public function view(User $user, CorrectionApproval $approval): bool
    {
        return $user->can('correction-approval.view');
    }

    public function create(User $user): bool
    {
        return $user->can('correction-approval.create');
    }

    public function update(User $user, CorrectionApproval $approval): bool
    {
        return $user->can('correction-approval.update');
    }

    public function delete(User $user, CorrectionApproval $approval): bool
    {
        return $user->can('correction-approval.delete');
    }

    public function approve(User $user, CorrectionApproval $approval): bool
    {
        return $user->can('correction-approval.approve');
    }

    public function reject(User $user, CorrectionApproval $approval): bool
    {
        return $user->can('correction-approval.reject');
    }

    public function escalate(User $user, CorrectionApproval $approval): bool
    {
        return $user->can('correction-approval.escalate');
    }
}