<?php

namespace App\Http\Controllers\v1;

use App\Enums\CivilStatus;
use App\Http\Controllers\Controller;
use App\Models\ApplicantInternship;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use ReflectionClass;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MOAController extends Controller
{
    /**
     * Helper to safely format Enum or String values
     */
    private function formatEnumOrString(mixed $value, string $default = 'Single'): string
    {
        if ($value instanceof CivilStatus) {
            return $value->label();
        }
        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }
        if ($value instanceof \UnitEnum) {
            return $value->name;
        }
        return !empty($value) ? (string) $value : $default;
    }

    /**
     * Convert Japanese prefecture names to proper Romaji suffix (-ken, -to, -fu)
     * "Aichi Prefecture" → "Aichi-ken"
     * "Tokyo"            → "Tokyo-to"
     * "Osaka"            → "Osaka-fu"
     * "Hokkaido"         → "Hokkaido"
     */
    private function formatJapanesePrefecture(?string $rawPrefecture): string
    {
        $clean = trim((string) $rawPrefecture);
        if ($clean === '') {
            return '';
        }

        // Strip "Prefecture", "Pref.", "Pref"
        $clean = trim(preg_replace('/\s*(prefecture|pref\.|pref)$/i', '', $clean));
        if ($clean === '') {
            return '';
        }

        $lower = strtolower($clean);

        // Already has proper suffix
        if (preg_match('/-(ken|to|fu|do)$/i', $clean)) {
            return $clean;
        }
        if (preg_match('/\s+(ken|to|fu|do)$/i', $clean, $m)) {
            return preg_replace('/\s+(ken|to|fu|do)$/i', '-' . strtolower($m[1]), $clean);
        }

        return match ($lower) {
            'tokyo', 'tōkyō', 'toukyou' => 'Tokyo-to',
            'osaka', 'ōsaka'             => 'Osaka-fu',
            'kyoto', 'kyōto'             => 'Kyoto-fu',
            'hokkaido', 'hokkaidō'       => 'Hokkaido',
            default                      => Str::title($clean) . '-ken',
        };
    }

    /**
     * Smart Location Formatter (Philippine MOA signing venue)
     */
    private function formatLocation(?string $rawLocation): string
    {
        $clean = trim((string) $rawLocation);
        if ($clean === '') {
            return 'Municipality of Murcia';
        }

        $lower = Str::lower($clean);

        if (Str::startsWith($lower, 'city of') || Str::startsWith($lower, 'municipality of')) {
            return Str::title($clean);
        }

        $knownCities = [
            'bacolod',
            'talisay',
            'silay',
            'bago',
            'san carlos',
            'kabankalan',
            'victorias',
            'cadiz',
            'sagay',
            'sipalay',
            'himamaylan',
            'escalante',
            'manila',
            'cebu',
            'davao',
            'iloilo',
            'cagayan de oro',
        ];

        $nameWithoutCity = trim(str_ireplace('city', '', $clean));
        $isCity = Str::endsWith($lower, 'city') || in_array(Str::lower($nameWithoutCity), $knownCities, true);

        if ($isCity) {
            return 'City of ' . Str::title($nameWithoutCity);
        }

        return 'Municipality of ' . Str::title($clean);
    }

    /**
     * Clean split XML tags inside {{...}} in tempDocumentMainPart
     */
    private function sanitizeMainDocumentXml(TemplateProcessor $templateProcessor): void
    {
        $reflection = new ReflectionClass($templateProcessor);
        if (!$reflection->hasProperty('tempDocumentMainPart')) {
            return;
        }

        $property = $reflection->getProperty('tempDocumentMainPart');
        $property->setAccessible(true);
        $xml = $property->getValue($templateProcessor);

        if (is_string($xml)) {
            $xml = preg_replace('/\{\s*<[^>]+>\s*\{/', '{{', $xml);
            $xml = preg_replace('/\}\s*<[^>]+>\s*\}/', '}}', $xml);

            $xml = preg_replace_callback('/\{\{(.*?)\}\}/s', function ($matches) {
                return '{{' . strip_tags($matches[1]) . '}}';
            }, $xml);

            $property->setValue($templateProcessor, $xml);
        }
    }

    public function generateMOA($internshipId): BinaryFileResponse
    {
        // 🎯 Support both Internship ID and Applicant ID lookup
        $internship = ApplicantInternship::with([
            'applicant.guarantors',
            'receivingCompany',
            'acceptingCompany',
            'dispatchingCompany',
            'program',
            'batch',
        ])
            ->where('id', $internshipId)
            ->orWhere('applicant_id', $internshipId)
            ->latest('id')
            ->firstOrFail();

        $applicant  = $internship->applicant;
        $guarantors = $applicant->guarantors->sortBy('sequence')->values();
        $g1         = $guarantors->get(0);
        $g2         = $guarantors->get(1);

        // Date calculations
        $agreementDate = $internship->agreement_date
            ? Carbon::parse($internship->agreement_date)
            : now();

        $sDay   = $agreementDate->format('jS');
        $sMonth = $agreementDate->format('F');
        $sYear  = $agreementDate->format('Y');

        $startDate = $internship->contract_start
            ? Carbon::parse($internship->contract_start)
            : null;

        // 🎯 Calculate end date from contract_start + contract_years if contract_end is null
        $contractYears = (int) ($internship->contract_years ?? 3);

        $endDate = $internship->contract_end
            ? Carbon::parse($internship->contract_end)
            : ($startDate ? $startDate->copy()->addYears($contractYears)->subDay() : null);

        $internAge = $applicant->date_of_birth
            ? Carbon::parse($applicant->date_of_birth)->age
            : ($applicant->age ?: '___');

        $internAddress = trim(implode(', ', array_filter([
            $applicant->current_address ?: $applicant->permanent_address,
            $applicant->city,
            $applicant->province,
            'Philippines',
        ])));

        $rawCityInput      = $internship->municipality ?? $applicant->city ?? 'Murcia';
        $formattedLocation = $this->formatLocation($rawCityInput);

        // 🎯 Search both resources and storage template directories
        $templatePaths = [
            resource_path('templates/MOA_MASTER_TEMPLATE.docx'),
            storage_path('app/templates/MOA_MASTER_TEMPLATE.docx'),
            resource_path('templates/MOA_MASTER_TEMPLATE.doc'),
        ];

        $templatePath = null;
        foreach ($templatePaths as $path) {
            if (file_exists($path) && filesize($path) > 0) {
                $templatePath = $path;
                break;
            }
        }

        if (!$templatePath) {
            abort(404, 'Template file MOA_MASTER_TEMPLATE.docx not found in resources/templates or storage/app/templates.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // 🎯 Clean XML split tags before setting macros
        $this->sanitizeMainDocumentXml($templateProcessor);
        $templateProcessor->setMacroChars('{{', '}}');

        $formatCertIssued = function ($g) {
            if (!$g) {
                return '__________________';
            }

            $issued = $g->residence_cert_issued_at
                ? Carbon::parse($g->residence_cert_issued_at)->format('m/d/Y')
                : '';

            if (!empty($g->residence_cert_place)) {
                $issued = trim($issued . ' - ' . Str::upper($g->residence_cert_place));
            }

            return $issued !== '' ? $issued : '__________________';
        };

        $company = $internship->receivingCompany;

        // 🎯 Format prefecture as -ken (e.g. "Aichi Prefecture" → "Aichi-ken")
        $prefecture = $this->formatJapanesePrefecture($company?->prefecture);

        $placeParts = array_filter([
            $prefecture,                                           // Aichi-ken
            $company?->city,                                       // City
            $company?->site_address ?: $company?->address,         // Street
        ]);

        $placeOfInternship = !empty($placeParts)
            ? implode(', ', $placeParts)
            : ($internship->place_of_internship ?? '________________________________');

        $templateProcessor->setValues([
            // 🎯 ALL DATE TAG ALIASES
            'DAY'     => $sDay,
            'DAY_S'   => $sDay,
            'S_DAY'   => $sDay,

            'MONTH'   => $sMonth,
            'MONTH_S' => $sMonth,
            'S_MONTH' => $sMonth,

            'YEAR'    => $sYear,
            'YEAR_S'  => $sYear,
            'S_YEAR'  => $sYear,

            // Locations
            'LOCATION_FULL' => $formattedLocation,
            'CITY'          => Str::title($rawCityInput),

            // Intern
            'I_NAME'             => Str::upper($applicant->full_name),
            'I_AGE'              => (string) $internAge,
            'I_STAT'             => $this->formatEnumOrString($applicant->civil_status, 'Single'),
            'I_ADDR'             => $internAddress ?: '________________________________________________',
            'I_PASSPORT'         => $applicant->passport_number ?? '___________',
            'INTERN_PASS_ISSUED' => $applicant->passport_expiry
                ? Carbon::parse($applicant->passport_expiry)->format('m/d/Y') . ' - DFA BACOLOD'
                : '__________________',

            // Guarantor 1
            'G1_NAME'        => $g1 ? Str::upper($g1->full_name) : '________________________',
            'G1_AGE'         => (string) ($g1?->age ?: '___'),
            'G1_STAT'        => $this->formatEnumOrString($g1?->civil_status, 'Single'),
            'G1_ADDR'        => $g1?->address ?: $internAddress,
            'G1_CER'         => $g1?->residence_cert_no ?? '___________',
            'G1_CERT_ISSUED' => $formatCertIssued($g1),

            // Guarantor 2
            'G2_NAME'        => $g2 ? Str::upper($g2->full_name) : '________________________',
            'G2_AGE'         => (string) ($g2?->age ?: '___'),
            'G2_STAT'        => $this->formatEnumOrString($g2?->civil_status, 'Single'),
            'G2_ADDR'        => $g2?->address ?: $internAddress,
            'G2_CER'         => $g2?->residence_cert_no ?? '___________',
            'G2_CERT_ISSUED' => $formatCertIssued($g2),

            // Company / Contract
            'RECEIVING_COMPANY' => Str::upper(
                $internship->receivingCompany?->name ?? '________________________'
            ),
            'CON_YEAR'  => $startDate ? $startDate->format('Y') : '____',
            'CON_MONTH' => $startDate ? $startDate->format('m') : '__',
            'CON_DAY'   => $startDate ? $startDate->format('d') : '__',
            'CE_YEAR'   => $endDate ? $endDate->format('Y') : '____',
            'CE_MONTH'  => $endDate ? $endDate->format('m') : '__',
            'CE_MON'    => $endDate ? $endDate->format('m') : '__',
            'CE_DAY'    => $endDate ? $endDate->format('d') : '__',

            // Internship details
            'PLACE_OF_INTERNSHIP' => $placeOfInternship,

            'JOB_DESCRIPTION' => Str::upper(
                $internship->job_description
                    ?? $internship->program?->default_job_description
                    ?? 'ASSEMBLING REINFORCED ROD BAR WORK'
            ),
            'SCHEDULE_DAYS' => $internship->work_days ?? 'MONDAY to SATURDAY',
            'DAYS_OFF'      => $internship->day_off ?? 'SUNDAY',
            'SCHED_START'   => $internship->time_start
                ? Carbon::parse($internship->time_start)->format('g:i A')
                : '8:00 AM',
            'SCHED_S'       => $internship->time_start
                ? Carbon::parse($internship->time_start)->format('g:i A')
                : '8:00 AM',
            'SCHED_END'     => $internship->time_end
                ? Carbon::parse($internship->time_end)->format('g:i A')
                : '6:00 PM',
            'LUNCH_BREAK'   => $internship->lunch_break ?? '40-50 mins.',

            // Pay
            'TOTAL_PAY' => number_format(
                ($internship->stipend_amount ?? 0) + ($internship->meal_allowance_amount ?? 0)
            ),
            'STIPEND'   => number_format($internship->stipend_amount ?? 0),
            'MEAL'      => number_format($internship->meal_allowance_amount ?? 0),
        ]);

        // Filename: Surname First Name Middle Name.docx
        $lastName   = trim($applicant->last_name ?? '');
        $firstName  = trim($applicant->first_name ?? '');
        $middleName = trim($applicant->middle_name ?? '');

        $nameParts = array_filter([$lastName, $firstName, $middleName]);

        if (!empty($nameParts)) {
            $displayName = implode(' ', $nameParts);
        } else {
            $displayName = $applicant->full_name ?? 'Applicant_MOA';
        }

        $cleanFileName = preg_replace('/[^\w\s,\.-]/u', '', $displayName);
        $fileName      = sprintf('%s.docx', trim($cleanFileName));

        $outputPath = tempnam(sys_get_temp_dir(), 'moa_dl_') . '.docx';
        $templateProcessor->saveAs($outputPath);

        return response()->download($outputPath, $fileName, [
            'Content-Type'                  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ])->deleteFileAfterSend(true);
    }
}
