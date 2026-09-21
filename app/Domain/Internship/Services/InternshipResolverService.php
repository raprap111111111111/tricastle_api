<?php

namespace App\Domain\Internship\Services;

use App\Domain\Internship\DTOs\GenerateMoaDTO;
use App\Enums\CivilStatus;
use App\Models\ApplicantInternship;
use App\Models\Company;
use App\Models\InternshipProgram;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InternshipResolverService
{
    public function resolve(ApplicantInternship $internship, ?GenerateMoaDTO $dto = null): array
    {
        $internship->loadMissing([
            'applicant.guarantors',
            'applicant.passportIssuingOffice',
            'batch',
            'program.dispatchingCompany',
            'program.acceptingCompany',
            'program.defaultReceivingCompany',
            'program.witnessUser',
            'dispatchingCompany',
            'acceptingCompany',
            'receivingCompany',
        ]);

        $applicant = $internship->applicant;
        $program   = $internship->program;
        $batch     = $internship->batch;

        // ── Companies Resolution ──────────────────────────────────────────────
        $dispatching = $internship->dispatchingCompany
            ?? $program?->dispatchingCompany
            ?? Company::query()->where('code', 'TRICASTLE')->first();

        $accepting = $internship->acceptingCompany
            ?? $batch?->acceptingCompany
            ?? $program?->acceptingCompany
            ?? Company::query()->where('code', 'MARUCON')->first();

        $receiving = $internship->receivingCompany
            ?? $batch?->receivingCompany
            ?? $program?->defaultReceivingCompany;

        // ── Dates Resolution (Date of Signing) ───────────────────────────────
        $agreementDate = Carbon::parse(
            $dto?->agreementDate ?? $internship->agreement_date ?? now()
        );

        $contractStart = Carbon::parse(
            $dto?->contractStart
                ?? $internship->contract_start
                ?? $batch?->contract_start
                ?? $agreementDate->copy()->addMonth()->startOfMonth()
        );

        $years = (int) (
            $dto?->contractYears
            ?? $internship->contract_years
            ?? $batch?->contract_years
            ?? $program?->contract_years
            ?? 3
        );

        $contractEnd = $dto?->contractEnd
            ? Carbon::parse($dto->contractEnd)
            : ($internship->contract_end
                ? Carbon::parse($internship->contract_end)
                : ($batch?->contract_end
                    ? Carbon::parse($batch->contract_end)
                    : $contractStart->copy()->addYears($years)));

        // ── Compensation ──────────────────────────────────────────────────────
        $stipend = (int) ($dto?->stipendAmount ?? $internship->stipend_amount ?? $batch?->stipend_amount ?? $program?->stipend_amount ?? 40000);
        $meal    = (int) ($dto?->mealAllowanceAmount ?? $internship->meal_allowance_amount ?? $batch?->meal_allowance_amount ?? $program?->meal_allowance_amount ?? 40000);

        // ── Job & Place ───────────────────────────────────────────────────────
        $job = $this->valueOrFallback(
            $dto?->jobDescription ?? $internship->job_description ?? $batch?->default_job_description ?? $applicant->trade_or_occupation ?? $applicant->applied_position ?? $program?->default_job_description,
            'FRAME WORKING'
        );

        $place = $this->valueOrFallback(
            $dto?->placeOfInternship ?? $internship->place_of_internship ?? $batch?->place_of_internship_override ?? $receiving?->site_address ?? $receiving?->address,
            'Ibaraki-ken Itako-city Magarimatsunami 430-1 /Assigned Jobsites'
        );

        $municipality = $this->valueOrFallback(
            $dto?->municipality ?? $internship->municipality ?? $batch?->municipality ?? $applicant->city ?? $program?->default_municipality,
            'Murcia'
        );

        // ── Intern Personal Data & Name Formatting ────────────────────────────
        // 🎯 Name Format: First Name + Middle Name + Last Name
        $nameParts = array_filter([
            $applicant->first_name,
            $applicant->middle_name,
            $applicant->last_name,
        ]);
        $formattedFullName = !empty($nameParts)
            ? implode(' ', $nameParts)
            : $applicant->full_name;

        // 🎯 Intern Address (Guaranteed to end with ", Philippines")
        $internRawAddress = $applicant->current_address ?: $applicant->permanent_address;
        $internAddress = $this->formatPhAddress($internRawAddress, $applicant->city, $applicant->province);

        $internAge = $applicant->date_of_birth
            ? Carbon::parse($applicant->date_of_birth)->age
            : ($applicant->age ?? 34);

        // ── Passport Issuance Format (e.g. 08/25/2011- DFA BAGUIO) ──────────
        $passIssuedDate = $applicant->passport_issue_date
            ? Carbon::parse($applicant->passport_issue_date)->format('m/d/Y')
            : null;

        $rawOfficeName = $applicant->passportIssuingOffice?->name
            ?? $applicant->passport_issue_place
            ?? null;

        $passIssuedPlace = $this->formatDfaOfficeName($rawOfficeName);

        if ($passIssuedDate && $passIssuedPlace) {
            // Tighter format matching Leah Tinsay's line (22 chars max)
            $internPassIssuedFormatted = "{$passIssuedDate}- {$passIssuedPlace}";
        } elseif ($passIssuedDate) {
            $internPassIssuedFormatted = $passIssuedDate;
        } elseif ($passIssuedPlace) {
            $internPassIssuedFormatted = $passIssuedPlace;
        } else {
            $internPassIssuedFormatted = 'DFA BACOLOD';
        }

        // ── Guarantors Pre-filling & Address Formatting ─────────────────────────
        $guarantors = $applicant->guarantors->sortBy('sequence')->values();

        $mapG = function ($g) use ($internAddress) {
            if (!$g) {
                return [
                    'full_name'             => '',
                    'age'                   => '',
                    'civil_status'          => 'Single',
                    'nationality'           => 'Filipino',
                    'address'               => '',
                    'residence_cert_no'     => '',
                    'residence_cert_issued' => '',
                ];
            }

            // 1. Dynamic Age
            $calculatedAge = !empty($g->date_of_birth)
                ? Carbon::parse($g->date_of_birth)->age
                : ($g->age ?: '');

            // 2. Dynamic Cedula Date & Place
            $certDate = $g->residence_cert_issued_at
                ? Carbon::parse($g->residence_cert_issued_at)->format('m/d/Y')
                : null;

            $certPlace = !empty($g->residence_cert_place)
                ? Str::upper(trim((string) $g->residence_cert_place))
                : null;

            if ($certDate && $certPlace) {
                $issued = "{$certDate} - {$certPlace}";
            } elseif ($certDate) {
                $issued = $certDate;
            } elseif ($certPlace) {
                $issued = $certPlace;
            } else {
                $issued = '';
            }

            // 🎯 Guarantor Address (Guaranteed to end with ", Philippines")
            $rawGAddress = $g->address ?? $g->permanent_address ?? null;
            $formattedGAddress = !empty($rawGAddress)
                ? $this->formatPhAddress($rawGAddress, $g->municipality ?? $g->city ?? null, $g->province ?? null)
                : $internAddress;

            return [
                'full_name'             => Str::upper(trim((string) ($g->full_name ?? ''))),
                'age'                   => $calculatedAge,
                'civil_status'          => $this->formatCivilStatus($g->civil_status),
                'nationality'           => !empty($g->nationality) ? Str::title((string) $g->nationality) : 'Filipino',
                'address'               => $formattedGAddress,
                'residence_cert_no'     => $g->residence_cert_no ?? '',
                'residence_cert_issued' => $issued,
            ];
        };

        $witnessUser = $program?->witnessUser;

        return [
            'internship_id'         => $internship->id,
            'program_type'          => $internship->program_type?->value ?? $internship->program_type ?? 'titp',
            'document_template'     => $program?->document_template ?: 'moa.template',

            // 🎯 Date of Signing Placeholders
            'agreement_date_raw'    => $agreementDate->toDateString(),
            'agreement_day_ordinal' => $agreementDate->format('jS'),             // e.g. "28th"
            'agreement_month'       => Str::upper($agreementDate->format('F')), // e.g. "SEPTEMBER"
            'agreement_year'        => $agreementDate->format('Y'),              // e.g. "2026"
            'municipality'          => $municipality,

            // Dispatching Organization
            'org' => [
                'name'                => $this->valueOrFallback($dispatching?->name_on_document ?? $dispatching?->name, 'TRICASTLE INTERNATIONAL INC.'),
                'address'             => $this->valueOrFallback($dispatching?->address, 'No.41 Roxas Avenue Brgy. 39, Bacolod City 6100 Philippines'),
                'president'           => $this->valueOrFallback($dispatching?->signatory_name, 'LEAH J. TINSAY'),
                'signatory_title'     => $this->valueOrFallback($dispatching?->signatory_title, 'President'),
                'passport'            => $this->valueOrFallback($dispatching?->signatory_passport, 'P6522838B'),
                'ack_passport'        => $this->valueOrFallback($dispatching?->signatory_id_no, 'P1825298D'),
                'ack_passport_issued' => $this->valueOrFallback($dispatching?->signatory_id_issued, '03/19/2021 - DFA MANILA'),
            ],

            // Accepting Organization
            'partner' => [
                'name'     => $this->valueOrFallback($accepting?->name_on_document ?? $accepting?->name, 'MARUCON'),
                'director' => $this->valueOrFallback($accepting?->signatory_name, 'Toshiki Koyama'),
                'title'    => $this->valueOrFallback($accepting?->signatory_title, 'Managing Director Multi-Contractors Cooperative (MARUCON)'),
            ],

            'receiving_company'   => $this->valueOrFallback($receiving?->name_on_document ?? $receiving?->name, 'SOWA KOGYO'),
            'place_of_internship' => $place,
            'job_description'     => Str::upper((string) $job),

            'contract_start_raw' => $contractStart->toDateString(),
            'contract_end_raw'   => $contractEnd->toDateString(),
            'contract_start_y'   => $contractStart->format('Y'),
            'contract_start_m'   => $contractStart->format('m'),
            'contract_start_d'   => $contractStart->format('d'),
            'contract_end_y'     => $contractEnd->format('Y'),
            'contract_end_m'     => $contractEnd->format('m'),
            'contract_end_d'     => $contractEnd->format('d'),
            'contract_years'     => $years,

            'compensation' => [
                'stipend'        => $stipend,
                'meal_allowance' => $meal,
                'total'          => $stipend + $meal,
                'currency'       => $program?->compensation_currency ?? 'JPY',
                'has_bonus'      => (bool) ($program?->has_bonus ?? false),
                'type'           => $program?->compensation_type ?? 'allowance',
            ],

            'schedule' => [
                'days'        => $internship->work_days ?? $batch?->work_days ?? $program?->work_days ?? 'MONDAY to SATURDAY',
                'day_off'     => $internship->day_off ?? $batch?->day_off ?? $program?->day_off ?? 'SUNDAY',
                'start'       => $internship->time_start ?? $batch?->time_start ?? $program?->time_start ?? '8:00 AM',
                'end'         => $internship->time_end ?? $batch?->time_end ?? $program?->time_end ?? '6:00 PM',
                'lunch_break' => $internship->lunch_break ?? $batch?->lunch_break ?? $program?->lunch_break ?? '40-50 mins.',
                'overtime'    => $program?->overtime_policy ?? 'Optional / Variable / Flexible',
            ],

            'witness' => [
                'name'  => $this->valueOrFallback($witnessUser?->full_name ?? $program?->witness_name, 'Ariel D. Taocta'),
                'title' => $this->valueOrFallback($witnessUser?->position ?? $program?->witness_title, 'Administrative Staff'),
                'org'   => $this->valueOrFallback($program?->witness_org, 'Tricastle International Inc.'),
            ],

            'intern' => [
                'full_name'             => Str::upper((string) $formattedFullName),
                'age'                   => $internAge,
                'civil_status'          => $this->formatCivilStatus($applicant->civil_status),
                'nationality'           => !empty($applicant->nationality) ? Str::title((string) $applicant->nationality) : 'Filipino',
                'address'               => $internAddress,
                'passport'              => $applicant->passport_number ?: 'P1677275D',
                'passport_issued_info'  => $internPassIssuedFormatted,
                'code'                  => $applicant->applicant_code,
            ],

            'guarantor_1' => $mapG($guarantors->get(0)),
            'guarantor_2' => $mapG($guarantors->get(1)),

            'dispatching_company_id' => $dispatching?->id,
            'accepting_company_id'   => $accepting?->id,
            'receiving_company_id'   => $receiving?->id,
            'internship_program_id'  => $program?->id,
            'batch_id'               => $batch?->id,
        ];
    }

    /**
     * Format Philippine address cleanly to end with ", Philippines"
     */
    private function formatPhAddress(?string $mainAddress, ?string $city = null, ?string $province = null): string
    {
        $parts = [];
        if (!empty($mainAddress)) {
            $parts[] = trim($mainAddress);
        }
        if (!empty($city) && !str_contains(strtolower($mainAddress ?? ''), strtolower($city))) {
            $parts[] = trim($city);
        }
        if (!empty($province) && !str_contains(strtolower($mainAddress ?? ''), strtolower($province))) {
            $parts[] = trim($province);
        }

        $full = implode(', ', array_filter($parts));
        $full = trim($full, ', ');

        if (empty($full)) {
            return 'Hda. Josefa II, Brgy. Blumentritt, Murcia, Negros Occidental, Philippines';
        }

        // Ensure address ends cleanly with ", Philippines"
        if (!preg_match('/philippines$/i', $full) && !preg_match('/, ph$/i', $full)) {
            $full .= ', Philippines';
        }

        return $full;
    }

    private function formatCivilStatus(mixed $status): string
    {
        if (empty($status)) {
            return 'Single';
        }

        if ($status instanceof CivilStatus) {
            return $status->label();
        }

        if ($status instanceof \BackedEnum) {
            return Str::title((string) $status->value);
        }

        $enum = CivilStatus::tryFrom((string) $status);
        if ($enum) {
            return $enum->label();
        }

        return Str::title((string) $status);
    }

    private function valueOrFallback(?string $value, string $fallback): string
    {
        return (!empty($value) && trim($value) !== '') ? trim($value) : $fallback;
    }

    public function resolveProgram(?int $programId, string $programType = 'titp'): ?InternshipProgram
    {
        if ($programId) {
            return InternshipProgram::query()->find($programId);
        }

        return InternshipProgram::query()
            ->active()
            ->where('program_type', $programType)
            ->latest('id')
            ->first();
    }

    /**
     * Cleans long DFA office names like "DFA Regional Consular Office – Baguio City"
     * into a short clean string like "DFA - BAGUIO CITY".
     */
    /**
     * Cleans long DFA office names into a short clean format (max ~10 chars)
     * Example: "DFA Regional Consular Office – Baguio City" → "DFA BAGUIO"
     */
    private function formatDfaOfficeName(?string $rawName): string
    {
        if (empty($rawName)) {
            return 'DFA BACOLOD';
        }

        $name = $rawName;

        // 1. Remove parenthetical notes e.g. (SM Manila)
        $name = preg_replace('/\s*\(.*?\)/', '', $name);

        // 2. Remove verbose phrases
        $verbose = [
            'Regional Consular Office',
            'Passport Service Program',
            'Consular Office',
            'POW',
        ];
        foreach ($verbose as $term) {
            $name = preg_replace('/' . preg_quote($term, '/') . '/i', '', $name);
        }

        // 3. Convert all dashes to space
        $name = str_replace(['–', '—', '-'], ' ', $name);

        // 4. Remove redundant "City" suffix to prevent Word tab-stop line overflow
        $name = preg_replace('/\s+City$/i', '', trim($name));

        // 5. Collapse multiple spaces
        $name = trim(preg_replace('/\s+/', ' ', $name));

        // 6. Remove leading "DFA" so we can re-add it cleanly
        $name = preg_replace('/^DFA\s*/i', '', $name);
        $name = trim($name);

        if (empty($name)) {
            return 'DFA BACOLOD';
        }

        return Str::upper('DFA ' . $name);
    }
}
