<?php

namespace App\Http\Controllers\v1\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ProfileUpdate\GetAllProfileUpdateRequest;
use App\Domain\ProfileUpdate\Repositories\ProfileUpdateRepository;
use App\Domain\ProfileUpdate\Actions\ApproveProfileUpdateAction;
use App\Domain\ProfileUpdate\Actions\DeclineProfileUpdateAction;
use App\Http\Resources\v1\ApplicantProfileUpdateResource;
use App\Models\ApplicantProfileUpdate;
use Illuminate\Http\Request;

class ProfileUpdateRequestController extends Controller
{
    public function index(GetAllProfileUpdateRequest $request, ProfileUpdateRepository $repository)
    {
        $result = $repository->getPaginatedByStatus(
            $request->validated(),
            ApplicantProfileUpdateResource::class
        );

        return response()->json($result);
    }

    public function approve(
        Request $request,
        ApplicantProfileUpdate $profileUpdate,
        ApproveProfileUpdateAction $action
    ) {
        if ($profileUpdate->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can be approved.'], 422);
        }

        $action->execute($profileUpdate, $request->user()->id);

        // Reload with reviewer so response includes who approved it
        $profileUpdate->load([
            'applicant:id,first_name,middle_name,last_name,applicant_code',
            'reviewer:id,first_name,middle_name,last_name',
        ]);

        return response()->json([
            'message' => 'Profile update approved and merged into applicant record.',
            'data'    => new ApplicantProfileUpdateResource($profileUpdate),
        ]);
    }

    public function decline(
        Request $request,
        ApplicantProfileUpdate $profileUpdate,
        DeclineProfileUpdateAction $action
    ) {
        $request->validate(['decline_reason' => 'required|string|max:500']);

        if ($profileUpdate->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can be declined.'], 422);
        }

        $action->execute($profileUpdate, $request->decline_reason, $request->user()->id);

        $profileUpdate->load([
            'applicant:id,first_name,middle_name,last_name,applicant_code',
            'reviewer:id,first_name,middle_name,last_name',
        ]);

        return response()->json([
            'message' => 'Profile update request declined.',
            'data'    => new ApplicantProfileUpdateResource($profileUpdate),
        ]);
    }
}