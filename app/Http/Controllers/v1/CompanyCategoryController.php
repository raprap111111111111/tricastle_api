<?php

namespace App\Http\Controllers\v1;

use App\Domain\CompanyCategory\Actions\CreateCompanyCategoryAction;
use App\Domain\CompanyCategory\Actions\DeleteCompanyCategoryAction;
use App\Domain\CompanyCategory\Actions\ToggleCompanyCategoryStatusAction;
use App\Domain\CompanyCategory\Actions\UpdateCompanyCategoryAction;
use App\Domain\CompanyCategory\Mappers\CompanyCategoryMapper;
use App\Domain\CompanyCategory\Repositories\CompanyCategoryRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\CompanyCategory\DeleteCompanyCategoryRequest;
use App\Http\Requests\v1\CompanyCategory\GetAllCompanyCategoryRequest;
use App\Http\Requests\v1\CompanyCategory\GetCompanyCategoryRequest;
use App\Http\Requests\v1\CompanyCategory\StoreCompanyCategoryRequest;
use App\Http\Requests\v1\CompanyCategory\ToggleCompanyCategoryStatusRequest;
use App\Http\Requests\v1\CompanyCategory\UpdateCompanyCategoryRequest;
use App\Http\Resources\v1\CompanyCategoryResource;
use App\Models\CompanyCategory;
use Illuminate\Http\JsonResponse;

class CompanyCategoryController extends Controller
{
    public function __construct(
        private readonly CompanyCategoryRepository $repository,
        private readonly CreateCompanyCategoryAction $createAction,
        private readonly UpdateCompanyCategoryAction $updateAction,
        private readonly DeleteCompanyCategoryAction $deleteAction,
        private readonly ToggleCompanyCategoryStatusAction $toggleStatusAction,
    ) {}

    public function index(
        GetAllCompanyCategoryRequest $request
    ): JsonResponse {
        $result = $this->repository->paginate(
            $request->validated(),
            CompanyCategoryResource::class
        );

        return response()->json($result);
    }

    public function store(
        StoreCompanyCategoryRequest $request
    ): JsonResponse {
        $dto      = CompanyCategoryMapper::fromStoreRequest($request);
        $category = $this->createAction->execute($dto);

        return (new CompanyCategoryResource($category))
            ->additional(['message' => 'Company category created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        GetCompanyCategoryRequest $request,
        CompanyCategory $companyCategory
    ): JsonResponse {
        return (new CompanyCategoryResource($companyCategory))
            ->response()
            ->setStatusCode(200);
    }

    public function update(
        UpdateCompanyCategoryRequest $request,
        CompanyCategory $companyCategory
    ): JsonResponse {
        $dto      = CompanyCategoryMapper::fromUpdateRequest($request);
        $category = $this->updateAction->execute($companyCategory, $dto);

        return (new CompanyCategoryResource($category))
            ->additional(['message' => 'Company category updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(
        DeleteCompanyCategoryRequest $request,
        CompanyCategory $companyCategory
    ): JsonResponse {
        $this->deleteAction->execute($companyCategory);

        return response()->json([
            'message' => 'Company category deleted successfully.',
        ]);
    }

    public function toggleStatus(
        ToggleCompanyCategoryStatusRequest $request,
        CompanyCategory $companyCategory
    ): JsonResponse {
        $category = $this->toggleStatusAction->execute($companyCategory);

        return (new CompanyCategoryResource($category))
            ->additional(['message' => 'Company category status toggled successfully.'])
            ->response()
            ->setStatusCode(200);
    }
}