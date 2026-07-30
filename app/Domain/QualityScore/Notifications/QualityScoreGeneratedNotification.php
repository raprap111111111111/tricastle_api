<?php
// app/Domain/QualityScore/Notifications/QualityScoreGeneratedNotification.php

namespace App\Domain\QualityScore\Notifications;

use App\Domain\Notification\Notifications\BaseNotification;
use App\Models\QualityScore;

class QualityScoreGeneratedNotification extends BaseNotification
{
    public function __construct(
        private readonly QualityScore $score
    ) {}

    protected function buildData(): array
    {
        return [
            'title'        => '⚠️ Low Quality Score Alert',
            'message'      => "Applicant #{$this->score->applicant_id} scored {$this->score->overall_score} ({$this->score->grade}) with {$this->score->critical_mismatches} critical mismatches.",
            'action_url'   => "/applicants/{$this->score->applicant_id}/quality-score",
            'action_label' => 'View Score',
            'meta'         => [
                'applicant_id'        => $this->score->applicant_id,
                'overall_score'       => $this->score->overall_score,
                'grade'               => $this->score->grade,
                'critical_mismatches' => $this->score->critical_mismatches,
            ],
        ];
    }
}