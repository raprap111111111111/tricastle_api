<?php

namespace App\Policies;

use App\Models\OcrJob;
use App\Models\User;

class OcrJobPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ocr-job.viewAny');
    }

    public function view(User $user, OcrJob $ocrJob): bool
    {
        return $user->can('ocr-job.viewAny')
            || ($user->can('ocr-job.viewOwn') && $ocrJob->initiated_by === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->can('ocr-job.create');
    }

    public function update(User $user, OcrJob $ocrJob): bool
    {
        return $user->can('ocr-job.updateAny')
            || ($user->can('ocr-job.updateOwn') && $ocrJob->initiated_by === $user->id);
    }

    public function delete(User $user, OcrJob $ocrJob): bool
    {
        return $user->can('ocr-job.delete');
    }

    public function queue(User $user, OcrJob $ocrJob): bool
    {
        return $user->can('ocr-job.queue');
    }

    public function cancel(User $user, OcrJob $ocrJob): bool
    {
        return $user->can('ocr-job.cancel')
            || ($user->can('ocr-job.cancelOwn') && $ocrJob->initiated_by === $user->id);
    }

    public function retry(User $user, OcrJob $ocrJob): bool
    {
        return $user->can('ocr-job.retry');
    }

    public function review(User $user, OcrJob $ocrJob): bool
    {
        return $user->can('ocr-job.review');
    }
}