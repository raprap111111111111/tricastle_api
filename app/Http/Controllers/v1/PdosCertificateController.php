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
        ]);

        $applicants = Applicant::with(['applicant_batches.batch.company'])
            ->whereIn('id', $validated['applicant_ids'])
            ->get()
            ->sortBy(fn ($a) => array_search($a->id, $validated['applicant_ids']))
            ->values();

        $outputPath = $action->execute($applicants, [
            'pdos_date' => $validated['pdos_date'] ?? now()->format('F j, Y'),
        ]);

        return response()
            ->download($outputPath, 'PDOS_BATCH_CERTIFICATES.docx')
            ->deleteFileAfterSend(true);
    }
}