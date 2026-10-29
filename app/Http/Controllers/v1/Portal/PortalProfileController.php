<?php

namespace App\Http\Controllers\v1\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\v1\Portal\SubmitProfileUpdateRequest;
use App\Domain\ProfileUpdate\DTOs\SubmitProfileUpdateDTO;
use App\Domain\ProfileUpdate\Actions\SubmitProfileUpdateAction;
use App\Domain\ProfileUpdate\Actions\GetPortalMeAction;
use App\Models\ApplicantPortalAccount;

class PortalProfileController extends Controller
{
    public function me(Request $request, GetPortalMeAction $action)
    {
        $account = $request->user();

        if (!$account || !($account instanceof ApplicantPortalAccount)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $action->execute($account);

        return response()->json($data);
    }

    public function submitUpdate(SubmitProfileUpdateRequest $request, SubmitProfileUpdateAction $action)
    {
        $account = $request->user();

        if (!$account || !($account instanceof ApplicantPortalAccount) || !$account->applicant_id) {
            return response()->json(['message' => 'Unauthenticated or missing applicant profile.'], 401);
        }

        $dto = new SubmitProfileUpdateDTO(
            $account->applicant_id,
            $request->validated()
        );

        $update = $action->execute($dto);

        return response()->json([
            'message' => 'Update submitted successfully.',
            'update' => $update,
        ], 201);
    }
}