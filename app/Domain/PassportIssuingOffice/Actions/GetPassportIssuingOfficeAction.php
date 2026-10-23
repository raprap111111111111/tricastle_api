<?php

namespace App\Domain\PassportIssuingOffice\Actions;

use App\Models\PassportIssuingOffice;

class GetPassportIssuingOfficeAction
{
    public function execute(PassportIssuingOffice $office): PassportIssuingOffice
    {
        return $office;
    }
}