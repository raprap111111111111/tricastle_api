<?php

namespace App\Policies;

use App\Models\OcrFieldExtraction;
use App\Models\User;

class OcrFieldExtractionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ocr-field-extraction.viewAny');
    }

    public function view(User $user, OcrFieldExtraction $extraction): bool
    {
        return $user->can('ocr-field-extraction.viewAny');
    }

    public function create(User $user): bool
    {
        return $user->can('ocr-field-extraction.create');
    }

    public function update(User $user, OcrFieldExtraction $extraction): bool
    {
        return $user->can('ocr-field-extraction.update');
    }

    public function delete(User $user, OcrFieldExtraction $extraction): bool
    {
        return $user->can('ocr-field-extraction.delete');
    }

    public function correct(User $user, OcrFieldExtraction $extraction): bool
    {
        return $user->can('ocr-field-extraction.correct');
    }

    public function accept(User $user, OcrFieldExtraction $extraction): bool
    {
        return $user->can('ocr-field-extraction.accept');
    }

    public function reject(User $user, OcrFieldExtraction $extraction): bool
    {
        return $user->can('ocr-field-extraction.reject');
    }
}