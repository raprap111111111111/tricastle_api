<?php

namespace App\Http\Controllers;

use App\Models\ApplicantInternship;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MOAController extends Controller
{
    public function generateMOA($internshipId): BinaryFileResponse
    {
        $internship = ApplicantInternship::with([
            'applicant.guarantors',
            'receivingCompany',
            'acceptingCompany',
            'dispatchingCompany',
            'program',
            'batch',
        ])->findOrFail($internshipId);

        $applicant   = $internship->applicant;
        $guarantors  = $applicant->guarantors->sortBy('sequence')->values();
        $g1          = $guarantors->get(0);
        $g2          = $guarantors->get(1);

        $agreementDate = $internship->agreement_date ? Carbon::parse($internship->agreement_date) : now();
        $startDate     = $internship->contract_start ? Carbon::parse($internship->contract_start) : null;
        $endDate       = $internship->contract_end ? Carbon::parse($internship->contract_end) : null;

        $internAge = $applicant->date_of_birth
            ? Carbon::parse($applicant->date_of_birth)->age
            : ($applicant->age ?: '___');

        $internAddress = trim(implode(', ', array_filter([
            $applicant->current_address ?: $applicant->permanent_address,
            $applicant->city,
            $applicant->province,
            'Philippines',
        ])));

        $templatePath = storage_path('app/templates/MOA_MASTER_TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            abort(404, 'Template file MOA_MASTER_TEMPLATE.docx not found.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);
        $templateProcessor->setMacroChars('{{', '}}'); // 👈 ENABLES {{TAG}} SYNTAX

        $formatCertIssued = function ($g) {
            if (!$g) return '__________________';
            $issued = $g->residence_cert_issued_at ? Carbon::parse($g->residence_cert_issued_at)->format('m/d/Y') : '';
            if (!empty($g->residence_cert_place)) {
                $issued = trim($issued . ' - ' . Str::upper($g->residence_cert_place));
            }
            return !empty($issued) ? $issued : '__________________';
        };

        $templateProcessor->setValues([
            'DAY'                 => $agreementDate->format('jS'),
            'MONTH'               => $agreementDate->format('F'),
            'YEAR'                => $agreementDate->format('Y'),
            'CITY'                => $internship->municipality ?? $applicant->city ?? 'Murcia',

            'I_NAME'              => Str::upper($applicant->full_name),
            'I_AGE'               => (string) $internAge,
            'I_STAT'              => Str::title($applicant->civil_status ?? 'Single'),
            'I_ADDR'              => $internAddress ?: '________________________________________________',
            'I_PASSPORT'          => $applicant->passport_number ?? '___________',
            'INTERN_PASS_ISSUED'  => $applicant->passport_expiry
                ? Carbon::parse($applicant->passport_expiry)->format('m/d/Y') . ' - DFA BACOLOD'
                : '__________________',

            'G1_NAME'             => $g1 ? Str::upper($g1->full_name) : '________________________',
            'G1_AGE'              => (string) ($g1?->age ?: '___'),
            'G1_STAT'             => Str::title($g1?->civil_status ?? 'Single'),
            'G1_ADDR'             => $g1?->address ?: $internAddress,
            'G1_CER'              => $g1?->residence_cert_no ?? '___________',
            'G1_CERT_ISSUED'      => $formatCertIssued($g1),

            'G2_NAME'             => $g2 ? Str::upper($g2->full_name) : '________________________',
            'G2_AGE'              => (string) ($g2?->age ?: '___'),
            'G2_STAT'             => Str::title($g2?->civil_status ?? 'Single'),
            'G2_ADDR'             => $g2?->address ?: $internAddress,
            'G2_CER'              => $g2?->residence_cert_no ?? '___________',
            'G2_CERT_ISSUED'      => $formatCertIssued($g2),

            'RECEIVING_COMPANY'   => Str::upper($internship->receivingCompany?->name ?? '________________________'),
            'CON_YEAR'            => $startDate ? $startDate->format('Y') : '____',
            'CON_MONTH'           => $startDate ? $startDate->format('m') : '__',
            'CON_DAY'             => $startDate ? $startDate->format('d') : '__',
            'CE_YEAR'             => $endDate ? $endDate->format('Y') : '____',
            'CE_MONTH'            => $endDate ? $endDate->format('m') : '__',
            'CE_MON'              => $endDate ? $endDate->format('m') : '__',
            'CE_DAY'              => $endDate ? $endDate->format('d') : '__',

            'PLACE_OF_INTERNSHIP' => $internship->place_of_internship ?? $internship->receivingCompany?->address ?? '________________________________',
            'JOB_DESCRIPTION'     => Str::upper($internship->job_description ?? $internship->program?->default_job_description ?? 'FRAME WORKING'),
            'SCHEDULE_DAYS'       => $internship->work_days ?? 'MONDAY to SATURDAY',
            'DAYS_OFF'            => $internship->day_off ?? 'SUNDAY',
            'SCHED_START'         => $internship->time_start ? Carbon::parse($internship->time_start)->format('g:i A') : '8:00 AM',
            'SCHED_S'             => $internship->time_start ? Carbon::parse($internship->time_start)->format('g:i A') : '8:00 AM',
            'SCHED_END'           => $internship->time_end ? Carbon::parse($internship->time_end)->format('g:i A') : '6:00 PM',
            'LUNCH_BREAK'         => $internship->lunch_break ?? '40-50 mins.',

            'TOTAL_PAY'           => number_format(($internship->stipend_amount ?? 0) + ($internship->meal_allowance_amount ?? 0)),
            'STIPEND'             => number_format($internship->stipend_amount ?? 0),
            'MEAL'                => number_format($internship->meal_allowance_amount ?? 0),
        ]);

        $safeLastName = Str::slug($applicant->last_name ?? 'intern');
        $fileName = sprintf('MOA_%s_%s.docx', $safeLastName, now()->format('Ymd_His'));
        $outputPath = tempnam(sys_get_temp_dir(), 'moa_dl_') . '.docx';

        $templateProcessor->saveAs($outputPath);

        return response()->download($outputPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}