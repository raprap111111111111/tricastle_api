<?php

namespace App\Domain\Internship\Services;

use App\Domain\Internship\DTOs\GenerateMoaDTO;
use App\Models\ApplicantInternship;
use App\Models\Company;
use App\Models\InternshipProgram;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InternshipResolverService
{
    /**
     * Resolve final MOA/context values:
     * Request override > Internship > Batch > Program > Company Master > Safe Fallbacks
     */
    public function resolve(ApplicantInternship $internship, ?GenerateMoaDTO $dto = null): array
    {
        $internship->loadMissing([
            'applicant.guarantors',
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

        // ── Dates Resolution ──────────────────────────────────────────────────
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

        // ── Intern Personal Data & Address ────────────────────────────────────
        $internAddress = trim(implode(', ', array_filter([
            $applicant->current_address ?: $applicant->permanent_address,
            $applicant->city,
            $applicant->province,
            'Philippines',
        ])));

        if (empty($internAddress) || $internAddress === 'Philippines') {
            $internAddress = 'Hda. Josefa II, Brgy. Blumentritt, Murcia, Negros Occidental, Philippines';
        }

        $internAge = $applicant->date_of_birth
            ? Carbon::parse($applicant->date_of_birth)->age
            : ($applicant->age ?? 34);

        // ── Guarantors ────────────────────────────────────────────────────────
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

            $issued = $g->residence_cert_issued_at ? Carbon::parse($g->residence_cert_issued_at)->format('m/d/Y') : '';
            if (!empty($g->residence_cert_place)) {
                // 🛡️ FIXED: Replaced Unicode en-dash ' – ' with standard clean ASCII hyphen ' - '
                $issued = trim($issued . ' - ' . Str::upper($g->residence_cert_place));
            }

            return [
                'full_name'             => Str::upper(trim($g->full_name ?? '')),
                'age'                   => $g->age ?: '',
                'civil_status'          => !empty($g->civil_status) ? Str::title($g->civil_status) : 'Single',
                'nationality'           => !empty($g->nationality) ? Str::title($g->nationality) : 'Filipino',
                'address'               => !empty($g->address) ? $g->address : $internAddress,
                'residence_cert_no'     => $g->residence_cert_no ?? '',
                'residence_cert_issued' => $issued,
            ];
        };

        $witnessUser = $program?->witnessUser;

        return [
            'internship_id'         => $internship->id,
            'program_type'          => $internship->program_type?->value ?? $internship->program_type ?? 'titp',
            'document_template'     => $program?->document_template ?: 'moa.template',

            'agreement_date_raw'    => $agreementDate->toDateString(),
            'agreement_day_ordinal' => $agreementDate->format('jS'),
            'agreement_month'       => $agreementDate->format('F'),
            'agreement_year'        => $agreementDate->format('Y'),
            'municipality'          => $municipality,

            // Dispatching Organization (Tricastle defaults if empty)
            'org' => [
                'name'                => $this->valueOrFallback($dispatching?->name_on_document ?? $dispatching?->name, 'TRICASTLE INTERNATIONAL INC.'),
                'address'             => $this->valueOrFallback($dispatching?->address, 'No.41 Roxas Avenue Brgy. 39, Bacolod City 6100 Philippines'),
                'president'           => $this->valueOrFallback($dispatching?->signatory_name, 'LEAH J. TINSAY'),
                'signatory_title'     => $this->valueOrFallback($dispatching?->signatory_title, 'President'),
                'passport'            => $this->valueOrFallback($dispatching?->signatory_passport, 'P6522838B'),
                'ack_passport'        => $this->valueOrFallback($dispatching?->signatory_id_no, 'P1825298D'),
                'ack_passport_issued' => $this->valueOrFallback($dispatching?->signatory_id_issued, '03/19/2021- DFA MANILA'),
            ],

            // Accepting Organization (MARUCON defaults if empty)
            'partner' => [
                'name'     => $this->valueOrFallback($accepting?->name_on_document ?? $accepting?->name, 'MARUCON'),
                'director' => $this->valueOrFallback($accepting?->signatory_name, 'Toshiki Koyama'),
                'title'    => $this->valueOrFallback($accepting?->signatory_title, 'Managing Director Multi-Contractors Cooperative (MARUCON)'),
            ],

            'receiving_company'   => $this->valueOrFallback($receiving?->name_on_document ?? $receiving?->name, 'SOWA KOGYO'),
            'place_of_internship' => $place,
            'job_description'     => Str::upper($job),

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
                'full_name'       => Str::upper($applicant->full_name),
                'age'             => $internAge,
                'civil_status'    => !empty($applicant->civil_status) ? Str::title($applicant->civil_status) : 'Single',
                'nationality'     => !empty($applicant->nationality) ? Str::title($applicant->nationality) : 'Filipino',
                'address'         => $internAddress,
                'passport'        => $applicant->passport_number ?: 'P1677275D',
                'passport_expiry' => $applicant->passport_expiry
                    ? Carbon::parse($applicant->passport_expiry)->format('m/d/Y')
                    : '12/03/2026',
                'code'            => $applicant->applicant_code,
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

    private function valueOrFallback(?string $value, string $fallback): string
    {
        return (!empty($value) && trim($value) !== '') ? trim($value) : $fallback;
    }

    private function mapGuarantor($g): array
    {
        return [];
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
}