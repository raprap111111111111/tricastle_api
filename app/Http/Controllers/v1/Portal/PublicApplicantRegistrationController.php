<?php

namespace App\Http\Controllers\v1\Portal;

use App\Domain\Applicant\Services\DuplicateDetectionService;
use App\Domain\Portal\Actions\PublicRegisterApplicantAction;
use App\Domain\Portal\DTOs\PublicRegisterApplicantDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Portal\PublicRegisterApplicantRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicApplicantRegistrationController extends Controller
{
    public function __construct(
        private readonly DuplicateDetectionService $duplicateService,
    ) {}

    /**
     * 🟢 Public Duplicate Checker (Unauthenticated & Privacy-Safe)
     * POST /api/v1/public/check-duplicates
     */
    public function checkDuplicates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'           => ['nullable', 'email'],
            'first_name'      => ['nullable', 'string', 'max:100'],
            'middle_name'     => ['nullable', 'string', 'max:100'],
            'last_name'       => ['nullable', 'string', 'max:100'],
            'date_of_birth'   => ['nullable', 'date'],
            'passport_number' => ['nullable', 'string', 'max:50'],
        ]);

        $rawDuplicates = $this->duplicateService->check(
            data: $validated,
            batchId: null,
            excludeId: null,
        );

        // Sanitize messages so public applicants cannot inspect other applicants' personal data
        $sanitizedDuplicates = array_map(function ($dup) {
            return [
                'type'     => $dup['type'],
                'severity' => $dup['severity'],
                'message'  => match ($dup['type']) {
                    'email'           => 'This email address is already registered in our system.',
                    'passport'        => 'This passport number is already registered in our system.',
                    'similar_person'  => 'An applicant with the same name and birthdate already exists.',
                    default           => 'A duplicate record was found.',
                },
            ];
        }, $rawDuplicates);

        return response()->json([
            'success'        => true,
            'has_duplicates' => count($sanitizedDuplicates) > 0,
            'has_blockers'   => $this->duplicateService->hasBlockers($rawDuplicates),
            'has_warnings'   => count($this->duplicateService->getWarnings($rawDuplicates)) > 0,
            'duplicates'     => $sanitizedDuplicates,
        ]);
    }

    /**
     * 🟢 Public Registration Submit
     * POST /api/v1/public/apply
     */
    public function store(
        PublicRegisterApplicantRequest $request,
        PublicRegisterApplicantAction $action
    ): JsonResponse {
        $dto = PublicRegisterApplicantDTO::fromRequest($request);
        $result = $action->execute($dto);

        $applicant = $result['applicant'];
        $account   = $result['account'];

        $statusValue = is_object($applicant->status) && isset($applicant->status->value)
            ? $applicant->status->value
            : (string) $applicant->status;

        return response()->json([
            'success'   => true,
            'message'   => 'Registration successful. Your application is pending staff validation.',
            'applicant' => [
                'id'             => $applicant->id,
                'applicant_code' => $applicant->applicant_code,
                'full_name'      => $applicant->full_name,
                'status'         => $statusValue,
            ],
            'account' => [
                'id'    => $account->id,
                'email' => $account->email,
            ],
        ], 201);
    }
}