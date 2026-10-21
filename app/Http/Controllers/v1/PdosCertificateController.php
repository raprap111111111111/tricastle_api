<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\ApplicantInternship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class PdosCertificateController extends Controller
{
    /**
     * Generate 2-in-1 PDOS Certificate (.docx)
     * Handles internship_id, applicant_id, or array payloads
     */
    public function generate(Request $request, $internshipId = null)
    {
        // 1. Extract ID flexible from any common payload format
        $id = $internshipId
            ?? $request->input('internship_id')
            ?? $request->input('applicant_id')
            ?? $request->input('id')
            ?? $request->input('applicant_ids.0')
            ?? $request->input('internship_ids.0');

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant or Internship ID is required to generate PDOS.',
            ], 422);
        }

        // 2. Find Internship by Internship ID OR Applicant ID
        $internship = ApplicantInternship::with([
            'applicant',
            'receivingCompany',
            'acceptingCompany',
            'dispatchingCompany',
            'program',
        ])
            ->where('id', $id)
            ->orWhere('applicant_id', $id)
            ->latest()
            ->first();

        // 3. Fallback: If no internship record exists yet, load Applicant directly
        $applicant = $internship?->applicant ?? Applicant::find($id);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant record not found.',
            ], 404);
        }

        // 4. Populate Template Variables
        $applicantName = Str::upper($applicant->full_name);

        $skillCategory = Str::upper(
            $internship?->job_description
                ?? $internship?->program?->default_job_description
                ?? $applicant->skill
                ?? 'TECHNICAL INTERN'
        );

        $country = 'JAPAN'; // Static

        $foreignEmployer = Str::upper(
            $internship?->receivingCompany?->name_on_document
                ?? $internship?->receivingCompany?->name
                ?? 'N/A'
        );

        $pdosDate = ($request->input('pdos_date') || $internship?->pdos_seminar_date)
            ? Carbon::parse($request->input('pdos_date') ?? $internship->pdos_seminar_date)->format('F d, Y')
            : now()->format('F d, Y');

        $certNo = $request->input('cert_no')
            ?? $applicant->pdos_number
            ?? ('TRI' . now()->format('Y') . '_' . str_pad((string) $applicant->id, 5, '0', STR_PAD_LEFT));

        // 5. Load Word Template
        // ✅ New path:
        $templatePath = resource_path('templates/PDOS.docx');

        if (!file_exists($templatePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Template file PDOS.docx not found in storage/app/templates/',
            ], 404);
        }

        $templateProcessor = new TemplateProcessor($templatePath);
        $templateProcessor->setMacroChars('{{', '}}');

        // 6. Set Values for BOTH TOP and BOTTOM
        $templateProcessor->setValues([
            'APPLICANT_NAME_TOP'      => $applicantName,
            'COMPANY_CATEGORY_TOP'    => $skillCategory,
            'COUNTRY_TOP'             => $country,
            'COMPANY_TOP'             => $foreignEmployer,

            'APPLICANT_NAME_BOTTOM'   => $applicantName,
            'COMPANY_CATEGORY_BOTTOM' => $skillCategory,
            'COUNTRY_BOTTOM'          => $country,
            'COMPANY_BOTTOM'          => $foreignEmployer,

            'PDOS_DATE'               => $pdosDate,
            'CERT_NO'                 => $certNo,
            'PDOS_DATE_TOP'           => $pdosDate,
            'PDOS_DATE_BOTTOM'        => $pdosDate,
            'CERT_NO_TOP'             => $certNo,
            'CERT_NO_BOTTOM'          => $certNo,
        ]);

        // 7. Save and Stream File Download
        $fileName = sprintf('PDOS_%s_%s.docx', Str::slug($applicant->last_name ?? 'applicant'), now()->format('Ymd'));
        $outputPath = tempnam(sys_get_temp_dir(), 'pdos_') . '.docx';
        $templateProcessor->saveAs($outputPath);

        return response()->download($outputPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}
