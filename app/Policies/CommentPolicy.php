<?php

// app/Policies/CommentPolicy.php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('comment.viewAny');
    }

    public function view(User $user, Comment $comment): bool
    {
        // Internal comments only visible to staff
        if ($comment->is_internal) {
            return $user->can('comment.viewInternal');
        }

        return $user->can('comment.view');
    }

    public function create(User $user): bool
    {
        return $user->can('comment.create');
    }

    public function update(User $user, Comment $comment): bool
    {
        // Owner can update their own comment
        if ($comment->isOwnedBy($user)) {
            return $user->can('comment.updateOwn');
        }

        return $user->can('comment.updateAny');
    }

    public function delete(User $user, Comment $comment): bool
    {
        // Owner can delete their own comment
        if ($comment->isOwnedBy($user)) {
            return $user->can('comment.deleteOwn');
        }

        return $user->can('comment.deleteAny');
    }
}