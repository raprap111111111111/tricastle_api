<?php

namespace App\Enums;

enum OcrJobStatus: string
{
    case PENDING = 'pending';
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case PARTIAL = 'partial';
    case REQUIRES_REVIEW = 'requires_review';
    case CANCELLED = 'cancelled';
    case TIMEOUT = 'timeout';
    case RETRYING = 'retrying';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::QUEUED => 'In Queue',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::PARTIAL => 'Partially Completed',
            self::REQUIRES_REVIEW => 'Requires Review',
            self::CANCELLED => 'Cancelled',
            self::TIMEOUT => 'Timed Out',
            self::RETRYING => 'Retrying',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'secondary',
            self::QUEUED => 'info',
            self::PROCESSING => 'primary',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::PARTIAL => 'warning',
            self::REQUIRES_REVIEW => 'warning',
            self::CANCELLED => 'secondary',
            self::TIMEOUT => 'danger',
            self::RETRYING => 'info',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::QUEUED => 'list',
            self::PROCESSING => 'spinner',
            self::COMPLETED => 'check-circle',
            self::FAILED => 'times-circle',
            self::PARTIAL => 'exclamation-triangle',
            self::REQUIRES_REVIEW => 'eye',
            self::CANCELLED => 'ban',
            self::TIMEOUT => 'stopwatch',
            self::RETRYING => 'redo',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }

    /**
     * Is job in a final state (won't change)
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::FAILED,
            self::CANCELLED,
        ]);
    }

    /**
     * Is job currently active/running
     */
    public function isActive(): bool
    {
        return in_array($this, [
            self::QUEUED,
            self::PROCESSING,
            self::RETRYING,
        ]);
    }

    /**
     * Can be retried
     */
    public function canRetry(): bool
    {
        return in_array($this, [
            self::FAILED,
            self::TIMEOUT,
            self::CANCELLED,
        ]);
    }

    /**
     * Needs human intervention
     */
    public function needsHumanReview(): bool
    {
        return in_array($this, [
            self::REQUIRES_REVIEW,
            self::PARTIAL,
        ]);
    }

    /**
     * Is job successful
     */
    public function isSuccessful(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::PARTIAL,
        ]);
    }

    /**
     * Progress percentage for UI
     */
    public function progressPercentage(): int
    {
        return match($this) {
            self::PENDING => 0,
            self::QUEUED => 10,
            self::PROCESSING => 50,
            self::RETRYING => 60,
            self::REQUIRES_REVIEW => 80,
            self::PARTIAL => 85,
            self::COMPLETED => 100,
            self::FAILED, self::TIMEOUT, self::CANCELLED => 0,
        };
    }
}
