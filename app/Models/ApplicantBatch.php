<?php

namespace App\Models;

use App\Enums\ApplicantBatchStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
// ✅ Spatie Activitylog v5
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ApplicantBatch extends Pivot
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'applicant_batches';

    public $incrementing = true;

    protected $fillable = [
        'applicant_id',
        'batch_id',
        'status',
        'assigned_at',
        'interview_date',
        'medical_date',
        'exam_date',
        'accepted_at',
        'deployed_at',
        'exam_score',
        'interview_notes',
        'medical_notes',
        'rejection_reason',
        'remarks',
        'processed_by',
    ];

    protected $casts = [
        'status'         => ApplicantBatchStatus::class,
        'assigned_at'    => 'datetime',
        'interview_date' => 'date',
        'medical_date'   => 'date',
        'exam_date'      => 'date',
        'accepted_at'    => 'datetime',
        'deployed_at'    => 'datetime',
        'exam_score'     => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (ApplicantBatch $applicantBatch) {
            $applicantBatch->status      ??= ApplicantBatchStatus::ASSIGNED;
            $applicantBatch->assigned_at ??= now();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'applicant_id',
                'batch_id',
                'status',
                'interview_date',
                'medical_date',
                'exam_date',
                'accepted_at',
                'deployed_at',
                'exam_score',
                'rejection_reason',
                'processed_by',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges() // ✅ was dontSubmitEmptyLogs()
            ->useLogName('ApplicantBatch')
            ->setDescriptionForEvent(function (string $event) {
                $applicantName = $this->applicant?->applicant_code ?? "applicant #{$this->applicant_id}";
                $batchName     = $this->batch?->name ?? "batch #{$this->batch_id}";

                if ($event === 'updated' && $this->isDirty('status')) {
                    $newStatus = $this->status?->value ?? $this->status;
                    $readable  = str_replace('_', ' ', (string) $newStatus);

                    return "Changed {$applicantName} status in {$batchName} → {$readable}";
                }

                return match ($event) {
                    'created' => "Assigned {$applicantName} to {$batchName}",
                    'updated' => "Updated {$applicantName} in {$batchName}",
                    'deleted' => "Removed {$applicantName} from {$batchName}",
                    default   => "{$applicantName} in {$batchName} was {$event}",
                };
            });
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            ApplicantBatchStatus::REJECTED->value,
            ApplicantBatchStatus::WITHDRAWN->value,
        ]);
    }

    public function scopeDeployed(Builder $query): Builder
    {
        return $query->where('status', ApplicantBatchStatus::DEPLOYED->value);
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', ApplicantBatchStatus::ACCEPTED->value);
    }

    public function scopeForBatch(Builder $query, int $batchId): Builder
    {
        return $query->where('batch_id', $batchId);
    }

    public function scopeForApplicant(Builder $query, int $applicantId): Builder
    {
        return $query->where('applicant_id', $applicantId);
    }

    public function isDeployed(): bool
    {
        return $this->status === ApplicantBatchStatus::DEPLOYED;
    }

    public function isAccepted(): bool
    {
        return $this->status === ApplicantBatchStatus::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === ApplicantBatchStatus::REJECTED;
    }

    public function isWithdrawn(): bool
    {
        return $this->status === ApplicantBatchStatus::WITHDRAWN;
    }

    public function canProgress(): bool
    {
        return ! in_array($this->status, [
            ApplicantBatchStatus::REJECTED,
            ApplicantBatchStatus::WITHDRAWN,
            ApplicantBatchStatus::DEPLOYED,
        ], true);
    }
}