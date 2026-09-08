<?php

namespace App\Enums;

enum InternshipProgramType: string
{
    case Intern      = 'intern';
    case Titp        = 'titp';
    case Ssw         = 'ssw';
    case SswTransfer = 'ssw_transfer';

    public function label(): string
    {
        return match ($this) {
            self::Intern      => 'Internship Program',
            self::Titp        => 'Technical Intern Training Program',
            self::Ssw         => 'Specified Skilled Worker',
            self::SswTransfer => 'SSW Company Transfer',
        };
    }

    public function allowsCompanyTransfer(): bool
    {
        return match ($this) {
            self::Ssw, self::SswTransfer => true,
            self::Intern, self::Titp     => false,
        };
    }
}