<?php

namespace App\Domain\Applicant\Actions;

use App\Models\Applicant;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ExportAisWordAction
{
    /**
     * Load the modified AIS_FINAL.docx template, populate all placeholders, and stream download.
     */
    public function execute(Applicant $applicant): BinaryFileResponse
    {
        $applicant->loadMissing([
            'lifestyle',
            'family',
            'educations',
            'employments',
            'japanContacts',
            'assignedStaff',
        ]);

        $templatePath = resource_path('templates/AIS_FINAL.docx');

        if (!file_exists($templatePath)) {
            abort(404, "AIS Word template not found at: {$templatePath}");
        }

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'AIS_' . uniqid() . '.docx';
        copy($templatePath, $tempPath);

        $zip = new ZipArchive();
        if ($zip->open($tempPath) !== true) {
            abort(500, 'Could not open Word document template.');
        }

        $replacements = $this->resolveReplacements($applicant);

        // Process main document body and any headers/footers safely
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            if (
                $entryName === 'word/document.xml' ||
                str_starts_with($entryName, 'word/header') ||
                str_starts_with($entryName, 'word/footer')
            ) {
                $xml = $zip->getFromIndex($i);
                $xml = $this->processWordXml($xml, $replacements);
                $zip->addFromString($entryName, $xml);
            }
        }

        $zip->close();

        // Clean filename (e.g., AIS_BARIOGA_RALPH.docx)
        $cleanLastName  = strtoupper(Str::slug($applicant->last_name ?? 'APPLICANT', '_'));
        $cleanFirstName = strtoupper(Str::slug($applicant->first_name ?? '', '_'));
        $fileName       = trim("AIS_{$cleanLastName}_{$cleanFirstName}", '_') . '.docx';

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Safely processes Word OpenXML paragraph-by-paragraph without breaking table cells (<w:tc>) or rows (<w:tr>).
     */
    private function processWordXml(string $xml, array $replacements): string
    {
        // Process each <w:p> (paragraph) individually so we NEVER break table XML tags
        return preg_replace_callback('/<w:p\b[^>]*>.*?<\/w:p>/s', function ($pMatch) use ($replacements) {
            $pXml = $pMatch[0];

            // If this paragraph has no placeholder delimiters, leave it completely untouched
            if (!str_contains($pXml, '{') && !str_contains($pXml, '$')) {
                return $pXml;
            }

            // Extract all text inside <w:t> tags in this paragraph
            if (!preg_match_all('/<w:t\b[^>]*>(.*?)<\/w:t>/s', $pXml, $tMatches)) {
                return $pXml;
            }

            // Combine all split text runs in this paragraph
            $combinedText = implode('', $tMatches[1]);

            if (!str_contains($combinedText, '{') && !str_contains($combinedText, '$')) {
                return $pXml;
            }

            // Decode XML entities to ensure clean string matching
            $plainText = html_entity_decode($combinedText, ENT_XML1 | ENT_QUOTES, 'UTF-8');

            // Apply all placeholder replacements
            foreach ($replacements as $key => $val) {
                $cleanKey = trim($key, '{}$ ');
                $valStr   = (string) $val;

                // Match {{KEY}}, {{ KEY }}, ${KEY}, {KEY} (case-insensitive)
                $plainText = preg_replace('/\{\{\s*' . preg_quote($cleanKey, '/') . '\s*\}\}/i', $valStr, $plainText);
                $plainText = preg_replace('/\$\{\s*' . preg_quote($cleanKey, '/') . '\s*\}/i', $valStr, $plainText);
            }

            // Clean up any unmatched {{TOKEN}} placeholders gracefully
            $plainText = preg_replace('/\{\{[A-Za-z0-9_\s]+\}\}/', '', $plainText);

            // Re-encode safe XML text
            $xmlSafeText = htmlspecialchars($plainText, ENT_XML1 | ENT_QUOTES, 'UTF-8');

            // Put the entire replaced text into the first <w:t> run and empty subsequent <w:t> runs
            $first = true;
            return preg_replace_callback('/<w:t\b([^>]*)>(.*?)<\/w:t>/s', function () use (&$first, $xmlSafeText) {
                if ($first) {
                    $first = false;
                    return '<w:t xml:space="preserve">' . $xmlSafeText . '</w:t>';
                }
                return '<w:t></w:t>';
            }, $pXml);
        }, $xml);
    }

    private function resolveReplacements(Applicant $a): array
    {
        // Safe number helper
        $num = fn($val) => ($val !== null && $val !== '' && (float) $val > 0) ? (string) (float) $val : '';

        // Safe date formatter
        $formatDate = function ($date, string $format = 'Y.m.d'): string {
            if (empty($date)) return '';
            try {
                if ($date instanceof CarbonInterface) return $date->format($format);
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
        $expYrsVal = ($expMonths !== null && $expMonths > 0) ? (string) intdiv($expMonths, 12) : '0';
        $expMtsVal = ($expMonths !== null && $expMonths > 0) ? (string) ($expMonths % 12) : '0';

        $ovsMonths = $overseas?->duration_months;
        $ovsYrsVal = ($ovsMonths !== null && $ovsMonths > 0) ? (string) intdiv($ovsMonths, 12) : '0';
        $ovsMtsVal = ($ovsMonths !== null && $ovsMonths > 0) ? (string) ($ovsMonths % 12) : '0';

        $expSentence = $experience
            ? "{$a->first_name} has experienced working as {$experience->position}"
            : "{$a->first_name} has no previous work experience recorded";

        // ---------- EDUCATIONS ----------
        $educations = $a->educations ?? collect();
        $findEdu = function ($keywords) use ($educations) {
            return $educations->first(function ($edu) use ($keywords) {
                $lvl = strtolower($edu->education_level ?? $edu->level ?? '');
                foreach ((array) $keywords as $kw) {
                    if (str_contains($lvl, strtolower($kw))) return true;
                }
                return false;
            });
        };

        $voc = $findEdu(['vocational', 'tesda', 'trade', 'voc']);
        $shs = $findEdu(['senior high', 'senior', 'shs', 'grade 11', 'grade 12']);
        $hs  = $educations->first(function ($edu) {
            $lvl = strtolower($edu->education_level ?? $edu->level ?? '');
            return str_contains($lvl, 'high') && !str_contains($lvl, 'senior');
        }) ?? $findEdu(['secondary', 'junior high']);
        $col = $findEdu(['college', 'tertiary', 'bachelor', 'university']);

        // ---------- JAPAN CONTACTS ----------
        $marucon    = $a->japanContacts->firstWhere('affiliation_type', 'marucon');
        $nonMarucon = $a->japanContacts->firstWhere('affiliation_type', 'non_marucon');

        $maruconCompany    = $marucon?->company_name ?? $marucon?->company ?? '';
        $maruconRelation   = $marucon?->relation ?? $marucon?->relationship ?? '';
        $nonMaruconCompany = $nonMarucon?->company_name ?? $nonMarucon?->company ?? '';
        $nonMaruconRelation= $nonMarucon?->relation ?? $nonMarucon?->relationship ?? '';
        $nonMaruconContact = $nonMarucon?->contact_number ?? $nonMarucon?->contact_no ?? $nonMarucon?->phone ?? '';

        // ---------- FAMILY BACKGROUND SENTENCE ----------
        $livingArrangement = $family?->living_arrangement ?? $family?->living_situation ?? '';
        $birthOrderStr = '';
        if ($family?->birth_order) {
            $ordinals = [1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th', 5 => '5th', 6 => '6th', 7 => '7th', 8 => '8th', 9 => '9th', 10 => '10th'];
            $ord = $ordinals[$family->birth_order] ?? "{$family->birth_order}th";
            $birthOrderStr = "{$ord} child among " . ($family->siblings_count ?? 'several') . " siblings";
            if ($family->siblings_description) {
                $birthOrderStr .= " ({$family->siblings_description})";
            }
        }
        $familyBackgroundFull = trim("{$livingArrangement}. {$birthOrderStr}", '. ');

        return [
            // ===== HEADER =====
            'POSITION'                  => (string) ($a->applied_position ?? $a->trade_or_occupation ?? ''),
            'TRADE_TEST_TRY'            => (string) ($a->trade_test_try ?? '1st'),
            'TRADE_TEST_DATE'           => $formatDate($a->trade_test_date, 'Y.m.d'),

            // ===== NAMES =====
            'L_NAME'                    => strtoupper($a->last_name ?? ''),
            'LAST_NAME'                 => strtoupper($a->last_name ?? ''),
            'F_NAME'                    => strtoupper($a->first_name ?? ''),
            'FIRST_NAME'                => strtoupper($a->first_name ?? ''),
            'M_NAME'                    => strtoupper($a->middle_name ?? ''),
            'MIDDLE_NAME'               => strtoupper($a->middle_name ?? ''),
            'FULL_NAME'                 => strtoupper(trim("{$a->first_name} {$a->middle_name} {$a->last_name}")),

            // ===== PERSONAL INFO =====
            'CURRENT_ADDRESS'           => (string) ($a->current_address ?? ''),
            'CONTACT_NO'                => (string) ($a->mobile ?? $a->phone ?? ''),
            'BIRTH'                     => $formatDate($a->date_of_birth, 'Y.m.d'),
            'BIRTH_DATE'                => $formatDate($a->date_of_birth, 'Y.m.d'),
            'BIRTHPLACE'                => (string) ($a->birthplace ?? ''),
            'AGE'                       => (string) ($a->age ?? ''),
            'HEIGHT'                    => $num($a->height_cm),
            'Height'                    => $num($a->height_cm),
            'WEIGHT'                    => $num($a->weight_kg),
            'Weight'                    => $num($a->weight_kg),
            'NO_CHILD'                  => (string) ($a->number_of_children ?? 0),
            'RELIGION'                  => (string) ($a->religion ?? ''),
            'HAND'                      => ucfirst((string) ($a->dominant_hand ?? 'Right Hand')),
            'DOMINANT_HAND'             => ucfirst((string) ($a->dominant_hand ?? 'Right Hand')),
            'B_TYPE'                    => (string) ($a->blood_type ?? ''),
            'BLOOD_TYPE'                => (string) ($a->blood_type ?? ''),
            'E_LEVEL'                   => $a->english_proficiency_pct !== null ? (string) $a->english_proficiency_pct : '0',
            'ENGLISH_LEVEL'             => $a->english_proficiency_pct !== null ? "{$a->english_proficiency_pct}%" : '0%',
            'C_STATUS'                  => ucfirst((string) ($a->civil_status?->value ?? $a->civil_status ?? 'Single')),
            'CIVIL_STATUS'              => ucfirst((string) ($a->civil_status?->value ?? $a->civil_status ?? 'Single')),

            // ===== LIFESTYLE =====
            'SMOKE'                     => $lifestyle?->is_smoking ? 'Yes' : 'No',
            'SMOKING_STICKS'            => (string) ($lifestyle?->smoking_frequency ?? ($lifestyle?->is_smoking ? 'Yes' : 'None')),
            'DRINK'                     => $lifestyle?->is_drinking_alcohol ? 'Yes' : 'No',
            'BOTTLE_PER_DAY'            => (string) ($lifestyle?->drinking_frequency ?? ($lifestyle?->is_drinking_alcohol ? 'Yes' : 'None')),

            // ===== FAMILY BACKGROUND =====
            'FAMILY_BACKGROUND'         => $familyBackgroundFull ?: 'Living with parents',
            'SIBLINGS INFO'             => (string) ($family?->siblings_description ?? ''),
            'SIBLINGS_INFO'             => (string) ($family?->siblings_description ?? ''),
            'SIBLINGSINFO'              => (string) ($family?->siblings_description ?? ''),
            'SPOUSE INFO'               => (string) ($family?->spouse_name ?? ''),
            'SPOUSE_INFO'               => (string) ($family?->spouse_name ?? ''),
            'SPOUSE_NAME'               => (string) ($family?->spouse_name ?? ''),
            'SPOUSE_WORK'               => (string) ($family?->spouse_occupation ?? ''),
            'SPOUSE_SALARY'             => $family?->spouse_salary ? number_format((float) $family->spouse_salary, 2) : '',

            // ===== EDUCATIONAL ATTAINMENT =====
            'VOCATIONAL_SN'             => (string) ($voc?->school_name ?? ''),
            'VOC_REMARKS'               => (string) ($voc?->remarks ?? 'GRADUATED'),
            'VOC_S'                     => $formatDate($voc?->date_started, 'Y.m'),
            'VOC_E'                     => $formatDate($voc?->date_ended, 'Y.m'),

            'HIGH_SN'                   => (string) ($hs?->school_name ?? ''),
            'HS_REMARKS'                => (string) ($hs?->remarks ?? 'GRADUATED'),
            'HIGH_S'                    => $formatDate($hs?->date_started, 'Y.m'),
            'HIGH_E'                    => $formatDate($hs?->date_ended, 'Y.m'),

            'SENIOR_HIGH_SN'            => (string) ($shs?->school_name ?? ''),
            'SHS_REMARKS'               => (string) ($shs?->remarks ?? 'GRADUATED'),
            'SENIOR_S'                  => $formatDate($shs?->date_started, 'Y.m'),
            'SENIOR_E'                  => $formatDate($shs?->date_ended, 'Y.m'),

            'COLLEGE_SN'                => (string) ($col?->school_name ?? ''),
            'COL_REMARKS'               => (string) ($col?->remarks ?? 'GRADUATED'),
            'COLLEGE_S'                 => $formatDate($col?->date_started, 'Y.m'),
            'COLLEGE_E'                 => $formatDate($col?->date_ended, 'Y.m'),

            'REMARKS'                   => 'GRADUATED',

            // ===== WORK EXPERIENCE =====
            'EXP_NAME_SENTENCE'         => $expSentence,
            'EXP_POSITION'              => (string) ($experience?->position ?? ''),
            'YRS'                       => $expYrsVal,
            'MTS'                       => $expMtsVal,
            'PRESENT_JOB'               => (string) ($presentJob?->position ?? ''),
            'SALARY'                    => $presentJob?->salary ? number_format((float) $presentJob->salary, 2) : '',
            'PRESENT_SALARY'            => $presentJob?->salary ? number_format((float) $presentJob->salary, 2) : '',
            'OVERSEAS'                  => (string) ($overseas?->position ?? ''),
            'D_YRS'                     => $ovsYrsVal,
            'D_ YRS '                   => $ovsYrsVal,
            'D_MNT'                     => $ovsMtsVal,
            'D_ MNT '                   => $ovsMtsVal,

            // ===== JAPAN CONTACTS =====
            'KNOW_JAPAN_WORKER_MARUCON' => (string) ($marucon?->name ?? ''),
            'MARUCON_NAME'              => (string) ($marucon?->name ?? ''),
            'BATCH'                     => (string) ($marucon?->batch_no ?? ''),
            'MARUCON_BATCH'             => (string) ($marucon?->batch_no ?? ''),

            'KNOW_JAPAN_WORKER'         => (string) ($nonMarucon?->name ?? ''),
            'NON_MARUCON_NAME'          => (string) ($nonMarucon?->name ?? ''),
            'NON_MARUCON_CONTACT'       => $nonMaruconContact,

            'COMPANY'                   => $maruconCompany ?: $nonMaruconCompany,
            'MARUCON_COMPANY'           => $maruconCompany,
            'NON_MARUCON_COMPANY'       => $nonMaruconCompany,

            'RELATION'                  => $maruconRelation ?: $nonMaruconRelation,
            'MARUCON_RELATION'          => $maruconRelation,
            'NON_MARUCON_RELATION'      => $nonMaruconRelation,

            // ===== FOOTER =====
            'DATE_NOW'                  => now()->format('F d, Y'),
            'DATE_TODAY'                => now()->format('F d, Y'),
            'STAFF_NAME'                => (string) (Auth::user()?->full_name ?? Auth::user()?->name ?? $a->assignedStaff?->full_name ?? 'Staff In-charge'),
        ];
    }
}