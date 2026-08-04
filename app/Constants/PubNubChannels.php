<?php

namespace App\Constants;

class PubNubChannels
{
    // Global channels
    public const APPLICANTS    = 'tricastle.applicants';
    public const BATCHES       = 'tricastle.batches';
    public const NOTIFICATIONS = 'tricastle.notifications';

    // User-specific channel
    public static function forUser(int $userId): string
    {
        return "tricastle.user.{$userId}";
    }

    // Applicant-specific channel
    public static function forApplicant(int $applicantId): string
    {
        return "tricastle.applicant.{$applicantId}";
    }

    // Batch-specific channel
    public static function forBatch(int $batchId): string
    {
        return "tricastle.batch.{$batchId}";
    }
}