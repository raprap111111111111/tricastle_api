<?php

namespace App\Http\Controllers\v1;

use App\Domain\Applicant\Actions\GeneratePdosBatchCertificateAction;
use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PdosCertificateController extends Controller
{
    public function generate(Request $request, GeneratePdosBatchCertificateAction $action): BinaryFileResponse
    {
        $validated = $request->validate([
            'applicant_ids'   => ['required', 'array', 'min:1'],
            'applicant_ids.*' => ['integer', 'exists:applicants,id'],
            'pdos_date'       => ['nullable', 'string'],
            'cert_no'         => ['nullable', 'string', 'max:100'],
        ]);

        $applicantIds = $validated['applicant_ids'];

        // Eager load company and its category
        $applicants = Applicant::with(['applicantBatches.batch.company.category'])
            ->whereIn('id', $applicantIds)
            ->get()
            ->sortBy(fn ($a) => array_search($a->id, $applicantIds))
            ->values();

        $outputPath = $action->execute($applicants, [
            'pdos_date' => $validated['pdos_date'] ?? now()->format('F j, Y'),
            'cert_no'   => $validated['cert_no'] ?? null,
        ]);

        return response()
            ->download($outputPath, 'PDOS_Certificates_' . now()->format('Y-m-d') . '.docx')
            ->deleteFileAfterSend(true);
    }
}