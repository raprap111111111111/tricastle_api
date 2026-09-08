<?php

namespace App\Http\Controllers\v1;

use App\Domain\Internship\Actions\BulkGenerateInternshipMoaAction;
use App\Domain\Internship\Actions\ChangeInternshipCompanyAction;
use App\Domain\Internship\Actions\CreateInternshipAction;
use App\Domain\Internship\Actions\DeleteInternshipAction;
use App\Domain\Internship\Actions\GenerateInternshipMoaAction;
use App\Domain\Internship\Actions\GetInternshipAction;
use App\Domain\Internship\Actions\ListInternshipsAction;
use App\Domain\Internship\Actions\UpdateInternshipAction;
use App\Domain\Internship\Mappers\InternshipMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Internship\BulkGenerateMoaRequest;
use App\Http\Requests\v1\Internship\ChangeCompanyRequest;
use App\Http\Requests\v1\Internship\GenerateMoaRequest;
use App\Http\Requests\v1\Internship\GetAllInternshipRequest;
use App\Http\Requests\v1\Internship\StoreInternshipRequest;
use App\Http\Requests\v1\Internship\UpdateInternshipRequest;
use App\Http\Resources\v1\InternshipDocumentResource;
use App\Http\Resources\v1\InternshipResource;
use App\Models\ApplicantInternship;
use Illuminate\Http\JsonResponse;

class InternshipController extends Controller
{
    public function __construct(
        private readonly ListInternshipsAction $listAction,
        private readonly GetInternshipAction $getAction,
        private readonly CreateInternshipAction $createAction,
        private readonly UpdateInternshipAction $updateAction,
        private readonly DeleteInternshipAction $deleteAction,
        private readonly ChangeInternshipCompanyAction $changeCompanyAction,
        private readonly GenerateInternshipMoaAction $generateMoaAction,
        private readonly BulkGenerateInternshipMoaAction $bulkGenerateMoaAction,
    ) {}

    public function index(GetAllInternshipRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            InternshipResource::class
        );

        return $this->responseSuccess($result, 'Internships retrieved successfully');
    }

    public function show(int $internship): JsonResponse
    {
        $model = $this->getAction->execute($internship);

        return $this->responseSuccess(
            new InternshipResource($model),
            'Internship retrieved successfully'
        );
    }

    public function store(StoreInternshipRequest $request): JsonResponse
    {
        $internship = $this->createAction->execute(
            InternshipMapper::fromCreateRequest($request)
        );

        $internship->load([
            'applicant', 'program', 'receivingCompany', 'acceptingCompany', 'dispatchingCompany', 'batch',
        ]);

        return $this->responseSuccess(
            new InternshipResource($internship),
            'Internship created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(UpdateInternshipRequest $request, ApplicantInternship $internship): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $internship,
            InternshipMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new InternshipResource($updated),
            'Internship updated successfully'
        );
    }

    public function destroy(ApplicantInternship $internship): JsonResponse
    {
        $this->deleteAction->execute($internship);

        return $this->responseSuccess(null, 'Internship deleted successfully');
    }

    public function changeCompany(ChangeCompanyRequest $request, ApplicantInternship $internship): JsonResponse
    {
        $newInternship = $this->changeCompanyAction->execute(
            $internship,
            InternshipMapper::fromChangeCompanyRequest($request)
        );

        $newInternship->load([
            'applicant', 'program', 'receivingCompany', 'previousInternship',
        ]);

        return $this->responseSuccess(
            new InternshipResource($newInternship),
            'Company changed successfully. New internship created.'
        );
    }

    public function generateMoa(GenerateMoaRequest $request, ApplicantInternship $internship): JsonResponse
    {
        $doc = $this->generateMoaAction->execute(
            $internship,
            InternshipMapper::fromGenerateMoaRequest($request)
        );

        return $this->responseSuccess(
            new InternshipDocumentResource($doc),
            'MOA generated successfully'
        );
    }

    public function bulkGenerateMoa(BulkGenerateMoaRequest $request): JsonResponse
    {
        $result = $this->bulkGenerateMoaAction->execute(
            internshipIds: $request->validated('internship_ids') ?? [],
            dto: InternshipMapper::fromBulkGenerateMoaRequest($request),
            batchId: $request->validated('batch_id'),
            onlyCurrent: $request->boolean('only_current', true),
        );

        return $this->responseSuccess([
            'type'         => $result['type'],
            'download_url' => $result['download'],
            'documents'    => InternshipDocumentResource::collection($result['documents']),
        ], 'Bulk MOA generated successfully');
    }
}