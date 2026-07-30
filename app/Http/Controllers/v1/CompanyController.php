<?php

namespace App\Http\Controllers\v1;

use App\Domain\Company\Actions\CreateCompanyAction;
use App\Domain\Company\Actions\DeleteCompanyAction;
use App\Domain\Company\Actions\ToggleCompanyStatusAction;
use App\Domain\Company\Actions\UpdateCompanyAction;
use App\Domain\Company\Mappers\CompanyMapper;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Company\DeleteCompanyRequest;
use App\Http\Requests\v1\Company\GetAllCompanyRequest;
use App\Http\Requests\v1\Company\GetCompanyRequest;
use App\Http\Requests\v1\Company\StoreCompanyRequest;
use App\Http\Requests\v1\Company\ToggleCompanyStatusRequest;
use App\Http\Requests\v1\Company\UpdateCompanyRequest;
use App\Http\Resources\v1\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyRepository $repository,
        private readonly CreateCompanyAction $createAction,
        private readonly UpdateCompanyAction $updateAction,
        private readonly DeleteCompanyAction $deleteAction,
        private readonly ToggleCompanyStatusAction $toggleStatusAction,
    ) {}

    public function index(
        GetAllCompanyRequest $request
    ): JsonResponse {
        $result = $this->repository->paginate(
            $request->validated(),
            CompanyResource::class
        );

        return response()->json($result);
    }

    public function store(
        StoreCompanyRequest $request
    ): JsonResponse {
        $dto     = CompanyMapper::fromStoreRequest($request);
        $company = $this->createAction->execute($dto);

        return (new CompanyResource($company->load('category')))
            ->additional(['message' => 'Company created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        GetCompanyRequest $request,
        Company $company
    ): JsonResponse {
        return (new CompanyResource($company->load('category')))
            ->response()
            ->setStatusCode(200);
    }

    public function update(
        UpdateCompanyRequest $request,
        Company $company
    ): JsonResponse {
        $dto     = CompanyMapper::fromUpdateRequest($request);
        $updated = $this->updateAction->execute($company, $dto);

        return (new CompanyResource($updated->load('category')))
            ->additional(['message' => 'Company updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(
        DeleteCompanyRequest $request,
        Company $company
    ): JsonResponse {
        $this->deleteAction->execute($company);

        return response()->json([
            'message' => 'Company deleted successfully.',
        ]);
    }

    public function toggleStatus(
        ToggleCompanyStatusRequest $request,
        Company $company
    ): JsonResponse {
        $updated = $this->toggleStatusAction->execute($company);

        return (new CompanyResource($updated->load('category')))
            ->additional(['message' => 'Company status toggled successfully.'])
            ->response()
            ->setStatusCode(200);
    }
}