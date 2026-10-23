<?php

namespace App\Http\Controllers\v1;

use App\Domain\PassportIssuingOffice\Actions\CreatePassportIssuingOfficeAction;
use App\Domain\PassportIssuingOffice\Actions\DeletePassportIssuingOfficeAction;
use App\Domain\PassportIssuingOffice\Actions\GetAllPassportIssuingOfficeAction;
use App\Domain\PassportIssuingOffice\Actions\GetPassportIssuingOfficeAction;
use App\Domain\PassportIssuingOffice\Actions\UpdatePassportIssuingOfficeAction;
use App\Domain\PassportIssuingOffice\Mappers\PassportIssuingOfficeMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\PassportIssuingOffice\DeletePassportIssuingOfficeRequest;
use App\Http\Requests\v1\PassportIssuingOffice\GetAllPassportIssuingOfficeRequest;
use App\Http\Requests\v1\PassportIssuingOffice\GetPassportIssuingOfficeRequest;
use App\Http\Requests\v1\PassportIssuingOffice\StorePassportIssuingOfficeRequest;
use App\Http\Requests\v1\PassportIssuingOffice\UpdatePassportIssuingOfficeRequest;
use App\Http\Resources\v1\PassportIssuingOfficeResource;
use App\Models\PassportIssuingOffice;
use Illuminate\Http\JsonResponse;

class PassportIssuingOfficeController extends Controller
{
    public function index(
        GetAllPassportIssuingOfficeRequest $request,
        GetAllPassportIssuingOfficeAction $action
    ): JsonResponse {
        $offices = $action->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Passport issuing offices retrieved successfully.',
            'data'    => PassportIssuingOfficeResource::collection($offices),
        ]);
    }

    public function store(
        StorePassportIssuingOfficeRequest $request,
        CreatePassportIssuingOfficeAction $action
    ): JsonResponse {
        $dto    = PassportIssuingOfficeMapper::fromCreateRequest($request);
        $office = $action->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Passport issuing office created successfully.',
            'data'    => new PassportIssuingOfficeResource($office),
        ], 201);
    }

    public function show(
        GetPassportIssuingOfficeRequest $request,
        PassportIssuingOffice $passportIssuingOffice,
        GetPassportIssuingOfficeAction $action
    ): JsonResponse {
        $office = $action->execute($passportIssuingOffice);

        return response()->json([
            'success' => true,
            'data'    => new PassportIssuingOfficeResource($office),
        ]);
    }

    public function update(
        UpdatePassportIssuingOfficeRequest $request,
        PassportIssuingOffice $passportIssuingOffice,
        UpdatePassportIssuingOfficeAction $action
    ): JsonResponse {
        $dto    = PassportIssuingOfficeMapper::fromUpdateRequest($request);
        $office = $action->execute($passportIssuingOffice, $dto);

        return response()->json([
            'success' => true,
            'message' => 'Passport issuing office updated successfully.',
            'data'    => new PassportIssuingOfficeResource($office),
        ]);
    }

    public function destroy(
        DeletePassportIssuingOfficeRequest $request,
        PassportIssuingOffice $passportIssuingOffice,
        DeletePassportIssuingOfficeAction $action
    ): JsonResponse {
        $action->execute($passportIssuingOffice);

        return response()->json([
            'success' => true,
            'message' => 'Passport issuing office deleted successfully.',
        ]);
    }
}