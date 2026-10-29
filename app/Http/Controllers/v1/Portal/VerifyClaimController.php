<?php

namespace App\Http\Controllers\v1\Portal;

use App\Domain\Portal\Actions\VerifyClaimAction;
use App\Domain\Portal\DTOs\VerifyClaimDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Portal\VerifyClaimRequest;
use Illuminate\Http\JsonResponse;

class VerifyClaimController extends Controller
{
    public function __invoke(VerifyClaimRequest $request, VerifyClaimAction $action): JsonResponse
    {
        $dto = new VerifyClaimDTO(
            firstName: (string) $request->input('first_name', ''),
            lastName: (string) $request->input('last_name', ''),
            dateOfBirth: (string) $request->input('date_of_birth', ''),
            middleName: $request->input('middle_name')
        );

        $result = $action->execute($dto);

        if (!$result) {
            return response()->json([
                'verified' => false,
                'message'  => 'Record not found. Ensure spelling and date of birth match your registration record.',
            ], 404);
        }

        return response()->json([
            'verified'     => true,
            'has_account'  => $result['has_account'],
            'applicant_id' => $result['applicant_id'],
            'message'      => 'Identity verified successfully.',
        ]);
    }
}