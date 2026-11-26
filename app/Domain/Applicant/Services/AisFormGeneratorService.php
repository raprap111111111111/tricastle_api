<?php

namespace App\Domain\Applicant\Services;

use App\Models\Applicant;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AisFormGeneratorService
{
    /**
     * Load the Excel template and populate all applicant placeholders.
     */
    public function build(Applicant $applicant): Spreadsheet
    {
        $applicant->loadMissing([
            'lifestyle',
            'family',
            'educations',
            'employments',
            'japanContacts',
        ]);

        $templatePath = resource_path('templates/AIS.xlsx');

        if (!file_exists($templatePath)) {
            $templatePath = resource_path('templates/AIS_TEMPLATE.xlsx');
        }

        if (!file_exists($templatePath)) {
            throw new \Exception("AIS template not found at: {$templatePath}");
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $values = $this->resolveReplacements($applicant);

        // 1. Replace all {{TOKEN}} placeholders
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);

            foreach ($cellIterator as $cell) {
                $text = (string) $cell->getValue();

                if (!str_contains($text, '{{')) {
                    continue;
                }

                $text = strtr($text, $values);
                // Case-insensitive cleanup of any unmapped placeholders
                $text = preg_replace('/\{\{[A-Za-z0-9_]+\}\}/i', '', $text);

                $cell->setValueExplicit($text, DataType::TYPE_STRING);
            }
        }

        // 2. Programmatically restore the Company Header and 2x2 Photo Box
        $this->applyHeaderAndPhotoBox($sheet);

        return $spreadsheet;
    }

    /**
     * Programmatically enforce the Header Banner and 2x2 Photo Box.
     * Prevents shapes or text boxes from being stripped by PhpSpreadsheet.
     */
    private function applyHeaderAndPhotoBox($sheet): void
    {
        $highestCol = $sheet->getHighestColumn();
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);

        // Find the address row dynamically
        $addressRow = 5;
        for ($r = 1; $r <= 10; $r++) {
            for ($c = 1; $c <= $highestColIndex; $c++) {
                $colLetter = Coordinate::stringFromColumnIndex($c);
                $val = (string) $sheet->getCell("{$colLetter}{$r}")->getValue();
                if (
                    stripos($val, 'Roxas') !== false ||
                    stripos($val, 'Rosas') !== false ||
                    stripos($val, 'Bacolod') !== false
                ) {
                    $addressRow = $r;
                    break 2;
                }
            }
        }

        $companyRow = max(1, $addressRow - 2);
        $licenseRow = max(2, $addressRow - 1);

        // Set readable row heights
        $sheet->getRowDimension($companyRow)->setRowHeight(22);
        $sheet->getRowDimension($licenseRow)->setRowHeight(16);

        $photoStartColIndex = max(1, $highestColIndex - 3);
        $photoStartCol = Coordinate::stringFromColumnIndex($photoStartColIndex);
        $photoEndCol   = $highestCol;

        $headerStartCol = 'C';
        $headerEndCol   = Coordinate::stringFromColumnIndex($photoStartColIndex - 1);

        // A. Company Title
        $companyRange = "{$headerStartCol}{$companyRow}:{$headerEndCol}{$companyRow}";
        $this->safeMergeCells($sheet, $companyRange);
        $sheet->setCellValue("{$headerStartCol}{$companyRow}", 'TRICASTLE INTERNATIONAL, INC.');
        $sheet->getStyle("{$headerStartCol}{$companyRow}")->getFont()
            ->setName('Arial Black')
            ->setSize(13)
            ->setBold(true);
        $sheet->getStyle("{$headerStartCol}{$companyRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // B. License
        $licenseRange = "{$headerStartCol}{$licenseRow}:{$headerEndCol}{$licenseRow}";
        $this->safeMergeCells($sheet, $licenseRange);
        $sheet->setCellValue("{$headerStartCol}{$licenseRow}", 'POEA - 119 - LB - 061918 - R');
        $sheet->getStyle("{$headerStartCol}{$licenseRow}")->getFont()
            ->setName('Arial')
            ->setSize(10)
            ->setBold(true);
        $sheet->getStyle("{$headerStartCol}{$licenseRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // C. 2x2 Photo Box
        $photoBottomRow = max(1, $addressRow);
        $photoRange = "{$photoStartCol}1:{$photoEndCol}{$photoBottomRow}";
        $this->safeMergeCells($sheet, $photoRange);

        $photoCell = "{$photoStartCol}1";
        $sheet->setCellValue($photoCell, "2x2\nPHOTO");
        $sheet->getStyle($photoCell)->getAlignment()
            ->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($photoCell)->getFont()
            ->setName('Arial')
            ->setSize(9)
            ->setBold(true)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('999999'));

        $sheet->getStyle($photoRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($photoRange)->getBorders()
            ->getOutline()
            ->setBorderStyle(Border::BORDER_MEDIUM);
    }

    /**
     * Unmerge overlapping cell ranges before applying a new merge.
     */
    private function safeMergeCells($sheet, string $range): void
    {
        [$start, $end] = explode(':', $range);
        $startCoord = Coordinate::coordinateFromString($start);
        $endCoord   = Coordinate::coordinateFromString($end);

        $startCol = Coordinate::columnIndexFromString($startCoord[0]);
        $endCol   = Coordinate::columnIndexFromString($endCoord[0]);
        $startRow = (int) $startCoord[1];
        $endRow   = (int) $endCoord[1];

        foreach ($sheet->getMergeCells() as $mergedRange) {
            if (!str_contains($mergedRange, ':')) {
                continue;
            }
            [$mStart, $mEnd] = explode(':', $mergedRange);
            $mStartCoord = Coordinate::coordinateFromString($mStart);
            $mEndCoord   = Coordinate::coordinateFromString($mEnd);

            $mStartCol = Coordinate::columnIndexFromString($mStartCoord[0]);
            $mEndCol   = Coordinate::columnIndexFromString($mEndCoord[0]);
            $mStartRow = (int) $mStartCoord[1];
            $mEndRow   = (int) $mEndCoord[1];

            $overlaps = !($endCol < $mStartCol || $startCol > $mEndCol || $endRow < $mStartRow || $startRow > $mEndRow);
            if ($overlaps) {
                $sheet->unmergeCells($mergedRange);
            }
        }

        $sheet->mergeCells($range);
    }

    /**
     * Map applicant data to template placeholder tags.
     * Supports BOTH old and new template token names.
     */
    private function resolveReplacements(Applicant $a): array
    {
        // Helper to format numbers cleanly (empty string if null/zero)
        $num = fn($val) => ($val !== null && $val !== '' && (float) $val > 0) ? (string) (float) $val : '';

        // Helper to safely format dates
        $formatDate = function ($date, string $format = 'M-Y'): string {
            if (empty($date)) {
                return '';
            }
            try {
                if ($date instanceof CarbonInterface) {
                    return $date->format($format);
                }
                return Carbon::parse($date)->format($format);
            } catch (\Throwable) {
                return (string) $date;
            }
        };

        $family    = $a->family;
        $lifestyle = $a->lifestyle;

        // ---------- EMPLOYMENT ----------
        $presentJob = $a->employments->firstWhere('is_current', true);
        $experience = $a->employments->where('is_current', false)->sortByDesc('date_started')->first() ?? $presentJob;
        $overseas   = $a->employments->first(function ($e) {
            return !empty($e->is_overseas) || str_contains(strtolower($e->location ?? ''), 'overseas') || str_contains(strtolower($e->country ?? ''), 'japan');
        });

        $expMonths = $experience?->duration_months;
        $expYrsVal = ($expMonths !== null && $expMonths > 0) ? (string) intdiv($expMonths, 12) : '';
        $expMtsVal = ($expMonths !== null && $expMonths > 0) ? (string) ($expMonths % 12) : '';

        $ovsMonths = $overseas?->duration_months;
        $ovsYrsVal = ($ovsMonths !== null && $ovsMonths > 0) ? (string) intdiv($ovsMonths, 12) : '';
        $ovsMtsVal = ($ovsMonths !== null && $ovsMonths > 0) ? (string) ($ovsMonths % 12) : '';

        // ---------- EDUCATIONS ----------
        $educations = $a->educations ?? collect();
        $findEdu = function ($keywords) use ($educations) {
            return $educations->first(function ($edu) use ($keywords) {
                $lvl = strtolower($edu->level?->value ?? $edu->level ?? '');
                foreach ((array) $keywords as $kw) {
                    if (str_contains($lvl, strtolower($kw))) {
                        return true;
                    }
                }
                return false;
            });
        };

        $voc = $findEdu(['vocational', 'tesda', 'trade', 'voc']);
        $shs = $findEdu(['senior high', 'senior', 'shs', 'grade 11', 'grade 12']);
        $hs  = $educations->first(function ($edu) {
            $lvl = strtolower($edu->level?->value ?? $edu->level ?? '');
            return str_contains($lvl, 'high') && !str_contains($lvl, 'senior');
        }) ?? $findEdu(['secondary', 'junior high']);
        $col = $findEdu(['college', 'tertiary', 'bachelor', 'university']);

        // ---------- JAPAN CONTACTS ----------
        $marucon    = $a->japanContacts->firstWhere('affiliation_type', 'marucon');
        $nonMarucon = $a->japanContacts->firstWhere('affiliation_type', 'non_marucon');

        $maruconCompany    = $marucon?->company ?? $marucon?->company_name ?? '';
        $maruconRelation   = $marucon?->relation ?? $marucon?->relationship ?? '';
        $nonMaruconCompany = $nonMarucon?->company ?? $nonMarucon?->company_name ?? '';
        $nonMaruconRel     = $nonMarucon?->relation ?? $nonMarucon?->relationship ?? '';
        $nonMaruconContact = $nonMarucon?->contact_number ?? $nonMarucon?->contact_no ?? $nonMarucon?->phone ?? '';

        return [
            // ===== HEADER =====
            '{{POSITION}}'            => (string) ($a->applied_position ?? ''),
            '{{TRADE_TEST_TRY}}'      => (string) ($a->trade_test_try ?? '1st'),
            '{{TRADE_TEST_DATE}}'     => $formatDate($a->trade_test_date, 'Y.m.d'),

            // ===== NAMES (Both old & new tokens) =====
            '{{L_NAME}}'              => strtoupper($a->last_name ?? ''),
            '{{LAST_NAME}}'           => strtoupper($a->last_name ?? ''),
            '{{F_NAME}}'              => strtoupper($a->first_name ?? ''),
            '{{FIRST_NAME}}'          => strtoupper($a->first_name ?? ''),
            '{{M_NAME}}'              => strtoupper($a->middle_name ?? ''),
            '{{MIDDLE_NAME}}'         => strtoupper($a->middle_name ?? ''),

            // ===== PERSONAL INFO =====
            '{{CURRENT_ADDRESS}}'     => (string) ($a->current_address ?? ''),
            '{{CONTACT_NO}}'          => (string) ($a->mobile ?? $a->phone ?? ''),
            '{{BIRTH}}'               => $formatDate($a->date_of_birth, 'Y.m.d'),
            '{{BIRTH_DATE}}'          => $formatDate($a->date_of_birth, 'Y.m.d'),
            '{{BIRTHPLACE}}'          => (string) ($a->birthplace ?? ''),
            '{{AGE}}'                 => (string) ($a->age ?? ''),
            '{{HEIGHT}}'              => $num($a->height_cm),
            '{{Height}}'              => $num($a->height_cm),
            '{{WEIGHT}}'              => $num($a->weight_kg),
            '{{Weight}}'              => $num($a->weight_kg),
            '{{NO_CHILD}}'            => (string) ($a->number_of_children ?? 0),
            '{{CHILDREN_COUNT}}'      => (string) ($a->number_of_children ?? 0),
            '{{RELIGION}}'            => (string) ($a->religion ?? ''),
            '{{HAND}}'                => (string) ($a->dominant_hand ?? 'Right Hand'),
            '{{DOMINANT_HAND}}'       => (string) ($a->dominant_hand ?? 'Right Hand'),
            '{{B_TYPE}}'              => (string) ($a->blood_type ?? ''),
            '{{BLOOD_TYPE}}'          => (string) ($a->blood_type ?? ''),
            '{{E_LEVEL}}'             => $a->english_proficiency_pct !== null ? (string) $a->english_proficiency_pct : '',
            '{{ENGLISH_LEVEL}}'       => $a->english_proficiency_pct !== null ? $a->english_proficiency_pct . '%' : '',
            '{{C_STATUS}}'            => Str::headline($a->civil_status?->value ?? $a->civil_status ?? ''),
            '{{CIVIL_STATUS}}'        => Str::headline($a->civil_status?->value ?? $a->civil_status ?? ''),

            // ===== LIFESTYLE =====
            '{{SMOKE}}'               => (string) ($lifestyle?->is_smoking ? 'Yes' : 'No'),
            '{{SMOKING_STICKS}}'      => (string) ($lifestyle?->smoking_frequency ?? ($lifestyle?->is_smoking ? 'Yes' : 'No')),
            '{{DRINK}}'               => (string) ($lifestyle?->is_drinking_alcohol ? 'Yes' : 'No'),
            '{{ALCOHOL_BOTTLES}}'     => (string) ($lifestyle?->drinking_frequency ?? ($lifestyle?->is_drinking_alcohol ? 'Yes' : 'No')),
            '{{BOTTLE_PER_DAY}}'      => (string) ($lifestyle?->drinking_frequency ?? ''),

            // ===== FAMILY BACKGROUND =====
            '{{FAMILY_BACKGROUND}}'   => (string) ($family?->living_arrangement ?? ''),
            '{{SIBLINGSINFO}}'        => (string) ($family?->siblings_description ?? ''),
            '{{SIBLINGS_INFO}}'       => (string) ($family?->siblings_description ?? ''),
            '{{SPOUSE_INFO}}'         => (string) ($family?->spouse_name ?? $a->spouse_name ?? ''),
            '{{SPOUSE_NAME}}'         => (string) ($family?->spouse_name ?? $a->spouse_name ?? ''),
            '{{SPOUSE_WORK}}'         => (string) ($family?->spouse_occupation ?? $a->spouse_occupation ?? ''),
            '{{SPOUSE_SALARY}}'       => $family?->spouse_salary ? number_format((float) $family->spouse_salary, 2) : '',

            // ===== EDUCATIONAL ATTAINMENT =====
            // Vocational
            '{{VOCATIONAL_SN}}'       => (string) ($voc?->school_name ?? ''),
            '{{VOCATIONAL_NAME}}'     => (string) ($voc?->school_name ?? ''),
            '{{VOC_REMARKS}}'         => (string) ($voc?->remarks ?? 'GRADUATED'),
            '{{VOC_S}}'               => $formatDate($voc?->date_started),
            '{{VOC_FROM}}'            => $formatDate($voc?->date_started),
            '{{VOC_E}}'               => $formatDate($voc?->date_ended),
            '{{VOC_TO}}'              => $formatDate($voc?->date_ended),

            // High School
            '{{HIGH_SN}}'             => (string) ($hs?->school_name ?? ''),
            '{{HIGHSCHOOL_NAME}}'     => (string) ($hs?->school_name ?? ''),
            '{{HS_REMARKS}}'          => (string) ($hs?->remarks ?? 'GRADUATED'),
            '{{HIGH_S}}'              => $formatDate($hs?->date_started),
            '{{HS_FROM}}'             => $formatDate($hs?->date_started),
            '{{HIGH_E}}'              => $formatDate($hs?->date_ended),
            '{{HS_TO}}'               => $formatDate($hs?->date_ended),

            // Senior High School
            '{{SENIOR_HIGH_SN}}'      => (string) ($shs?->school_name ?? ''),
            '{{SENIOR_HIGHSCHOOL_NAME}}' => (string) ($shs?->school_name ?? ''),
            '{{SHS_REMARKS}}'         => (string) ($shs?->remarks ?? 'GRADUATED'),
            '{{SENIOR_S}}'            => $formatDate($shs?->date_started),
            '{{SHS_FROM}}'            => $formatDate($shs?->date_started),
            '{{SENIOR_E}}'            => $formatDate($shs?->date_ended),
            '{{SHS_TO}}'              => $formatDate($shs?->date_ended),

            // College
            '{{COLLEGE_SN}}'          => (string) ($col?->school_name ?? ''),
            '{{COLLEGE_NAME}}'        => (string) ($col?->school_name ?? ''),
            '{{COL_REMARKS}}'         => (string) ($col?->remarks ?? 'GRADUATED'),
            '{{COLLEGE_S}}'           => $formatDate($col?->date_started),
            '{{COLLEGE_FROM}}'        => $formatDate($col?->date_started),
            '{{COLLEGE_E}}'           => $formatDate($col?->date_ended),
            '{{COLLEGE_TO}}'          => $formatDate($col?->date_ended),

            // Universal fallback for remarks
            '{{REMARKS}}'             => 'GRADUATED',

            // ===== WORK EXPERIENCE =====
            '{{EXP_POSITION}}'        => (string) ($experience?->position ?? ''),
            '{{EXP_DETAILS}}'         => (string) ($experience?->job_description ?? ''),
            '{{YRS}}'                 => $expYrsVal,
            '{{EXP_YEARS}}'           => $expYrsVal,
            '{{MTS}}'                 => $expMtsVal,
            '{{EXP_MONTHS}}'          => $expMtsVal,
            '{{PRESENT_JOB}}'         => (string) ($presentJob?->position ?? ''),
            '{{SALARY}}'              => $presentJob?->salary ? number_format((float) $presentJob->salary, 2) : '',
            '{{PRESENT_SALARY}}'      => $presentJob?->salary ? number_format((float) $presentJob->salary, 2) : '',

            '{{OVERSEAS}}'            => (string) ($overseas?->position ?? ''),
            '{{D_YRS}}'               => $ovsYrsVal,
            '{{D_MNT}}'               => $ovsMtsVal,

            // ===== JAPAN CONTACTS =====
            '{{KNOW_JAPAN_WORKER_MARUCON}}' => (string) ($marucon?->name ?? ''),
            '{{MARUCON_NAME}}'              => (string) ($marucon?->name ?? ''),
            '{{BATCH}}'                     => (string) ($marucon?->batch_no ?? ''),
            '{{MARUCON_BATCH}}'             => (string) ($marucon?->batch_no ?? ''),
            '{{MARUCON_COMPANY}}'           => $maruconCompany,
            '{{MARUCON_RELATION}}'          => $maruconRelation,

            '{{KNOW_JAPAN_WORKER}}'         => (string) ($nonMarucon?->name ?? ''),
            '{{NON_MARUCON_NAME}}'          => (string) ($nonMarucon?->name ?? ''),
            '{{NON_MARUCON_CONTACT}}'       => $nonMaruconContact,
            '{{NON_MARUCON_COMPANY}}'       => $nonMaruconCompany,
            '{{NON_MARUCON_RELATION}}'      => $nonMaruconRel,

            // Shared Fallbacks
            '{{COMPANY}}'                   => $maruconCompany ?: $nonMaruconCompany,
            '{{RELATION}}'                  => $maruconRelation ?: $nonMaruconRel,

            // ===== FOOTER / SIGNATURE =====
            '{{DATE_NOW}}'                  => now()->format('F d, Y'),
            '{{DATE_TODAY}}'                => now()->format('F d, Y'),
            '{{STAFF_NAME}}'                => (string) (Auth::user()?->name ?? 'Staff'),
        ];
    }
}