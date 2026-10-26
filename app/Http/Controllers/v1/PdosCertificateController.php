<?php

namespace App\Http\Controllers\v1;

use App\Domain\Applicant\Actions\GeneratePdosBatchCertificateAction;
use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\ApplicantInternship;
use Illuminate\Http\Request;

class PdosCertificateController extends Controller
{
    /**
     * Generate PDOS Certificates (.docx)
     * Handles single or multiple selected applicants (2 applicants per page).
     */
    public function generate(Request $request, $internshipId = null)
    {
        // 1. Capture applicant or internship IDs flexibly from ANY request format
        $rawApplicantIds = $request->input('applicant_ids')
            ?? $request->input('ids')
            ?? $request->input('selected_ids')
            ?? $request->input('applicants')
            ?? $request->input('applicant_id')
            ?? $request->input('id')
            ?? $internshipId;

        $rawInternshipIds = $request->input('internship_ids')
            ?? $request->input('internships')
            ?? $request->input('internship_id');

        // Convert string arrays or single integers to clean arrays
        $applicantIds = $this->normalizeIds($rawApplicantIds);
        $internshipIds = $this->normalizeIds($rawInternshipIds);

        $applicants = collect();

        // 2. Fetch Applicants with valid relationships
        if (! empty($applicantIds)) {
            $fetched = Applicant::with([
                'internships.receivingCompany.category',
                'applicantBatches.batch.company.category',
            ])->whereIn('id', $applicantIds)->get();

            // Preserve table selection order from UI
            $applicants = collect($applicantIds)->map(function ($id) use ($fetched) {
                return $fetched->firstWhere('id', (int) $id);
            })->filter()->values();
        }

        // Fallback: If passed IDs were Internship IDs
        if ($applicants->isEmpty() && ! empty($internshipIds)) {
            $internships = ApplicantInternship::with([
                'applicant.internships.receivingCompany.category',
                'applicant.applicantBatches.batch.company.category',
            ])->whereIn('id', $internshipIds)->get();

            $applicants = $internships->map(fn ($i) => $i->applicant)->filter()->unique('id')->values();
        }

        if ($applicants->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid applicant records found.',
            ], 404);
        }

        // 3. Delegate to Batch Action
        $action = app(GeneratePdosBatchCertificateAction::class);
        $outputPath = $action->execute($applicants, [
            'pdos_date' => $request->input('pdos_date'),
            'cert_no'   => $request->input('cert_no'),
        ]);

        $fileName = sprintf('PDOS_Certificates_%s.docx', now()->format('Ymd_His'));

        return response()->download($outputPath, $fileName, [
            'Content-Type'                  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ])->deleteFileAfterSend(true);
    }

    private function normalizeIds(mixed $input): array
    {
        if (empty($input)) {
            return [];
        }

        if (is_array($input)) {
            return array_map('intval', array_filter($input));
        }

        if (is_string($input)) {
            if (str_contains($input, ',')) {
                return array_map('intval', array_filter(explode(',', $input)));
            }
            return [(int) trim($input)];
        }

        if (is_numeric($input)) {
            return [(int) $input];
        }

        return [];
    }
}