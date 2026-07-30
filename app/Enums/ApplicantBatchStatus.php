<?php

namespace App\Enums;

enum ApplicantBatchStatus: string
{
    case APPLIED             = 'applied';
    case SHORTLISTED         = 'shortlisted';
    case INTERVIEW_SCHEDULED = 'interview_scheduled';
    case INTERVIEW_PASSED    = 'interview_passed';
    case INTERVIEW_FAILED    = 'interview_failed';
    case MEDICAL_PENDING     = 'medical_pending';
    case MEDICAL_PASSED      = 'medical_passed';
    case MEDICAL_FAILED      = 'medical_failed';
    case EXAM_PENDING        = 'exam_pending';
    case EXAM_PASSED         = 'exam_passed';
    case EXAM_FAILED         = 'exam_failed';
    case ACCEPTED            = 'accepted';
    case REJECTED            = 'rejected';
    case WITHDRAWN           = 'withdrawn';
    case DEPLOYED            = 'deployed';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::APPLIED             => 'Applied',
            self::SHORTLISTED         => 'Shortlisted',
            self::INTERVIEW_SCHEDULED => 'Interview Scheduled',
            self::INTERVIEW_PASSED    => 'Interview Passed',
            self::INTERVIEW_FAILED    => 'Interview Failed',
            self::MEDICAL_PENDING     => 'Medical Pending',
            self::MEDICAL_PASSED      => 'Medical Passed',
            self::MEDICAL_FAILED      => 'Medical Failed',
            self::EXAM_PENDING        => 'Exam Pending',
            self::EXAM_PASSED         => 'Exam Passed',
            self::EXAM_FAILED         => 'Exam Failed',
            self::ACCEPTED            => 'Accepted',
            self::REJECTED            => 'Rejected',
            self::WITHDRAWN           => 'Withdrawn',
            self::DEPLOYED            => 'Deployed',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::REJECTED,
            self::WITHDRAWN,
            self::DEPLOYED,
        ], true);
    }
}