<?php

namespace App\Http\Controllers\v1\Portal;

use App\Domain\Portal\Actions\PublicRegisterApplicantAction;
use App\Domain\Portal\DTOs\PublicRegisterApplicantDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Portal\PublicRegisterApplicantRequest;
use Illuminate\Http\JsonResponse;

class PublicApplicantRegistrationController extends Controller
{
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