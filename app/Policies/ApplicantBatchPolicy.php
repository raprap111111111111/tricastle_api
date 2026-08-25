<?php

namespace App\Policies;

use App\Models\ApplicantBatch;
use App\Models\User;

class ApplicantBatchPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('applicant-batch.viewAny');
    }

    public function view(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.view');
    }

    public function create(User $user): bool
    {
        return $user->can('applicant-batch.create');
    }

    public function update(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.update');
    }

    public function delete(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.delete');
    }

    public function restore(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.delete');
    }

    public function forceDelete(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.delete');
    }

    public function updateStatus(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.updateStatus');
    }

    public function scheduleInterview(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.scheduleInterview');
    }

    public function recordExam(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.recordExam');
    }

    public function accept(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.accept');
    }

    public function reject(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.reject');
    }

    public function withdraw(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.withdraw');
    }

    public function deploy(User $user, ApplicantBatch $applicantBatch): bool
    {
        return $user->can('applicant-batch.deploy');
    }
}