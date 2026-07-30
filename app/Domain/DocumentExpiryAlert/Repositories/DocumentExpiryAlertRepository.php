<?php
// app/Domain/DocumentExpiryAlert/Repositories/DocumentExpiryAlertRepository.php

namespace App\Domain\DocumentExpiryAlert\Repositories;

use App\Models\DocumentExpiryAlert;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DocumentExpiryAlertRepository extends BaseRepository
{
    protected string $model = DocumentExpiryAlert::class;

    protected array $relations  = ['applicantDocument', 'applicant'];
    protected array $searchable = [];
    protected array $filterable = ['alert_type', 'email_sent', 'notification_sent', 'applicant_id'];
    protected array $sortable   = ['created_at', 'expiry_date', 'days_until_expiry'];

    protected string $defaultOrderBy        = 'expiry_date';
    protected string $defaultOrderDirection = 'asc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->filled('applicant_document_id')) {
            $query->where('applicant_document_id', $request->input('applicant_document_id'));
        }

        return $query;
    }

    public function getPendingNotifications(): \Illuminate\Database\Eloquent\Collection
    {
        return DocumentExpiryAlert::query()
            ->where('notification_sent', false)
            ->get();
    }
}