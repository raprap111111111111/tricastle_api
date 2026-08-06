<?php
// app/Domain/Comment/Actions/UpdateCommentAction.php

namespace App\Domain\Comment\Actions;

use App\Domain\Comment\DTOs\UpdateCommentDTO;
use App\Domain\Comment\Repositories\CommentRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Applicant;
use App\Models\ApplicantDocument;
use App\Models\Batch;
use App\Models\Comment;

class UpdateCommentAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    public function execute(Comment $comment, UpdateCommentDTO $dto): Comment
    {
        $updated = $this->repository->update($comment, $dto);

        // 🔔 Notify people watching the resource that a comment was edited
        $this->sendEditNotifications($updated);

        return $updated;
    }

    /**
     * 🔔 Notify relevant people about the comment edit
     */
    private function sendEditNotifications(Comment $comment): void
    {
        $commentable = $comment->commentable;
        if (!$commentable) return;

        $authorId   = $comment->user_id ?? $comment->created_by;
        $authorName = $comment->user?->name ?? 'Someone';
        $excerpt    = str($comment->body ?? $comment->content ?? '')
                        ->limit(80)
                        ->toString();

        match (true) {
            $commentable instanceof Applicant         => $this->notifyOnApplicantEdit($commentable, $authorName, $excerpt, $authorId),
            $commentable instanceof ApplicantDocument => $this->notifyOnDocumentEdit($commentable, $authorName, $excerpt, $authorId),
            $commentable instanceof Batch             => $this->notifyOnBatchEdit($commentable, $authorName, $excerpt, $authorId),
            default                                   => null,
        };
    }

    private function notifyOnApplicantEdit(Applicant $applicant, string $authorName, string $excerpt, ?int $authorId): void
    {
        $name = "{$applicant->first_name} {$applicant->last_name}";
        $code = $applicant->applicant_code;

        $this->notifyUser(
            user:      $applicant->assigned_staff_id,
            title:     '✏️ Comment Edited',
            message:   "{$authorName} edited a comment on {$name} ({$code}): \"{$excerpt}\"",
            module:    'applicant',
            actionUrl: "/applicants/{$applicant->id}",
        );
    }

    private function notifyOnDocumentEdit(ApplicantDocument $document, string $authorName, string $excerpt, ?int $authorId): void
    {
        $applicant = $document->applicant;
        $docType   = $document->documentType?->name ?? 'Document';
        $name      = $applicant ? "{$applicant->first_name} {$applicant->last_name}" : 'Unknown';

        $this->notifyUser(
            user:      $document->uploaded_by,
            title:     '✏️ Comment Edited',
            message:   "{$authorName} edited a comment on {$docType} for {$name}: \"{$excerpt}\"",
            module:    'document',
            actionUrl: "/documents/{$document->id}",
        );
    }

    private function notifyOnBatchEdit(Batch $batch, string $authorName, string $excerpt, ?int $authorId): void
    {
        $this->notifyStaff(
            permissions: 'batch.viewAny',
            title:       '✏️ Batch Comment Edited',
            message:     "{$authorName} edited a comment on \"{$batch->name}\" (#{$batch->batch_number}): \"{$excerpt}\"",
            module:      'batch',
            actionUrl:   "/batches/{$batch->id}",
        );
    }
}