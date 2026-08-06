<?php
// app/Domain/Comment/Actions/CreateCommentAction.php

namespace App\Domain\Comment\Actions;

use App\Domain\Comment\DTOs\CreateCommentDTO;
use App\Domain\Comment\Repositories\CommentRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Applicant;
use App\Models\ApplicantDocument;
use App\Models\Batch;
use App\Models\Comment;

class CreateCommentAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    public function execute(CreateCommentDTO $dto): Comment
    {
        $comment = $this->repository->create($dto);

        // 🔔 Smart notification based on what was commented on
        $this->sendCommentNotifications($comment);

        return $comment;
    }

    /**
     * 🔔 Send notifications based on the commented resource
     * Comments can be on: Applicant, ApplicantDocument, Batch, etc.
     */
    private function sendCommentNotifications(Comment $comment): void
    {
        // Load the commentable (polymorphic parent)
        $commentable = $comment->commentable;
        if (!$commentable) return;

        $authorId    = $comment->user_id ?? $comment->created_by;
        $authorName  = $comment->user?->name ?? 'Someone';
        $excerpt     = str($comment->body ?? $comment->content ?? '')
                        ->limit(80)
                        ->toString();

        // 🎯 Route based on what type of thing was commented on
        match (true) {
            $commentable instanceof Applicant           => $this->notifyOnApplicant($commentable, $authorName, $excerpt, $authorId),
            $commentable instanceof ApplicantDocument   => $this->notifyOnDocument($commentable, $authorName, $excerpt, $authorId),
            $commentable instanceof Batch               => $this->notifyOnBatch($commentable, $authorName, $excerpt, $authorId),
            default                                     => null,
        };
    }

    /**
     * 💬 Comment on an APPLICANT
     */
    private function notifyOnApplicant(Applicant $applicant, string $authorName, string $excerpt, ?int $authorId): void
    {
        $name = "{$applicant->first_name} {$applicant->last_name}";
        $code = $applicant->applicant_code;

        // Notify assigned staff (they should know if someone comments)
        $this->notifyUser(
            user:      $applicant->assigned_staff_id,
            title:     '💬 New Comment on Your Applicant',
            message:   "{$authorName} commented on {$name} ({$code}): \"{$excerpt}\"",
            module:    'applicant',
            actionUrl: "/applicants/{$applicant->id}",
        );

        // Notify the creator (if different from author + assigned staff)
        if ($applicant->created_by
            && $applicant->created_by !== $authorId
            && $applicant->created_by !== $applicant->assigned_staff_id
        ) {
            $this->notifyUser(
                user:      $applicant->created_by,
                title:     '💬 New Comment',
                message:   "{$authorName} commented on {$name} ({$code}): \"{$excerpt}\"",
                module:    'applicant',
                actionUrl: "/applicants/{$applicant->id}",
            );
        }
    }

    /**
     * 💬 Comment on a DOCUMENT
     */
    private function notifyOnDocument(ApplicantDocument $document, string $authorName, string $excerpt, ?int $authorId): void
    {
        $applicant = $document->applicant;
        $docType   = $document->documentType?->name ?? 'Document';
        $name      = $applicant ? "{$applicant->first_name} {$applicant->last_name}" : 'Unknown';

        // Notify the uploader
        $this->notifyUser(
            user:      $document->uploaded_by,
            title:     '💬 New Comment on Your Document',
            message:   "{$authorName} commented on the {$docType} you uploaded for {$name}: \"{$excerpt}\"",
            module:    'document',
            actionUrl: "/documents/{$document->id}",
        );

        // Notify the assigned staff (if different from uploader)
        if ($applicant?->assigned_staff_id
            && $applicant->assigned_staff_id !== $document->uploaded_by
            && $applicant->assigned_staff_id !== $authorId
        ) {
            $this->notifyUser(
                user:      $applicant->assigned_staff_id,
                title:     '💬 New Comment on Document',
                message:   "{$authorName} commented on {$docType} for {$name}: \"{$excerpt}\"",
                module:    'document',
                actionUrl: "/documents/{$document->id}",
            );
        }
    }

    /**
     * 💬 Comment on a BATCH
     */
    private function notifyOnBatch(Batch $batch, string $authorName, string $excerpt, ?int $authorId): void
    {
        // Notify staff who can view batches
        $this->notifyStaff(
            permissions: 'batch.viewAny',
            title:       '💬 New Comment on Batch',
            message:     "{$authorName} commented on \"{$batch->name}\" (#{$batch->batch_number}): \"{$excerpt}\"",
            module:      'batch',
            actionUrl:   "/batches/{$batch->id}",
        );
    }
}