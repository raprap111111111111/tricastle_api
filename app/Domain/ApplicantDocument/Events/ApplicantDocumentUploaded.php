<?php
// app/Domain/ApplicantDocument/Events/ApplicantDocumentUploaded.php

namespace App\Domain\ApplicantDocument\Events;

use App\Models\ApplicantDocument;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicantDocumentUploaded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ApplicantDocument $document
    ) {}
}