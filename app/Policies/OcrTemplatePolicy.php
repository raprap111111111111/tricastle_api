<?php

// app/Policies/OcrTemplatePolicy.php

namespace App\Policies;

use App\Models\OcrTemplate;
use App\Models\User;

class OcrTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ocr-templates.viewAny');
    }

    public function view(User $user, OcrTemplate $ocrTemplate): bool
    {
        return $user->can('ocr-templates.view');
    }

    public function create(User $user): bool
    {
        return $user->can('ocr-templates.create');
    }

    public function update(User $user, OcrTemplate $ocrTemplate): bool
    {
        return $user->can('ocr-templates.update');
    }

    public function delete(User $user, OcrTemplate $ocrTemplate): bool
    {
        return $user->can('ocr-templates.delete');
    }

    public function approve(User $user, OcrTemplate $ocrTemplate): bool
    {
        return $user->can('ocr-templates.approve');
    }

    public function reject(User $user, OcrTemplate $ocrTemplate): bool
    {
        return $user->can('ocr-templates.reject');
    }

    public function complete(User $user, OcrTemplate $ocrTemplate): bool
    {
        return $user->can('ocr-templates.complete');
    }

    public function cancel(User $user, OcrTemplate $ocrTemplate): bool
    {
        return $user->can('ocr-templates.cancel');
    }
}