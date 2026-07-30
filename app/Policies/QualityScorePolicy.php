<?php

namespace App\Policies;

use App\Models\QualityScore;
use App\Models\User;

class QualityScorePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('quality-score.viewAny');
    }

    public function view(User $user, QualityScore $qualityScore): bool
    {
        return $user->can('quality-score.view');
    }

    public function create(User $user): bool
    {
        return $user->can('quality-score.create');
    }

    public function update(User $user, QualityScore $qualityScore): bool
    {
        return $user->can('quality-score.update');
    }

    public function delete(User $user, QualityScore $qualityScore): bool
    {
        return $user->can('quality-score.delete');
    }

    public function recalculate(User $user): bool
    {
        return $user->can('quality-score.recalculate');
    }
}