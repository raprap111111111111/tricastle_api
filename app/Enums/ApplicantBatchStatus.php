<?php
// app/Domain/ApplicantBatch/Enums/ApplicantBatchStatus.php

namespace App\Enums;

enum ApplicantBatchStatus: string
{
    case ASSIGNED            = 'assigned';
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

    public function label(): string
    {
        return match ($this) {
            self::ASSIGNED            => 'Assigned',
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

    public function color(): string
    {
        return match ($this) {
            self::ASSIGNED            => 'blue',
            self::INTERVIEW_SCHEDULED => 'cyan',
            self::INTERVIEW_PASSED    => 'teal',
            self::INTERVIEW_FAILED    => 'red',
            self::MEDICAL_PENDING     => 'orange',
            self::MEDICAL_PASSED      => 'teal',
            self::MEDICAL_FAILED      => 'red',
            self::EXAM_PENDING        => 'orange',
            self::EXAM_PASSED         => 'teal',
            self::EXAM_FAILED         => 'red',
            self::ACCEPTED            => 'green',
            self::REJECTED            => 'red',
            self::WITHDRAWN           => 'gray',
            self::DEPLOYED            => 'green',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function canProgress(): bool
    {
        return ! in_array($this, [
            self::REJECTED,
            self::WITHDRAWN,
            self::DEPLOYED,
        ]);
    }
}