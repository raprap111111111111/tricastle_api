<?php

namespace App\Http\Controllers\v1;

use App\Domain\Internship\Actions\SyncGuarantorsAction;
use App\Domain\Internship\Mappers\InternshipMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Internship\SyncGuarantorsRequest;
use App\Http\Resources\v1\ApplicantGuarantorResource;
use App\Models\Applicant;
use Illuminate\Http\JsonResponse;

class ApplicantGuarantorController extends Controller
{
    public function __construct(
        private readonly SyncGuarantorsAction $syncGuarantorsAction,
    ) {}

    public function index(Applicant $applicant): JsonResponse
    {
        return $this->responseSuccess(
            ApplicantGuarantorResource::collection($applicant->guarantors()->orderBy('sequence')->get()),
            'Guarantors retrieved successfully'
        );
    }

    public function sync(SyncGuarantorsRequest $request, Applicant $applicant): JsonResponse
    {
        $guarantors = $this->syncGuarantorsAction->execute(
            InternshipMapper::fromSyncGuarantorsRequest($request, $applicant->id)
        );

        return $this->responseSuccess(
            ApplicantGuarantorResource::collection($guarantors),
            'Guarantors synced successfully'
        );
    }
}