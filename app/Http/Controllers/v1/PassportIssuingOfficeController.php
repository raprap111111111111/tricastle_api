<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\PassportIssuingOffice;
use Illuminate\Http\JsonResponse;

class PassportIssuingOfficeController extends Controller
{
    /**
     * Display a listing of active DFA branches grouped by Region.
     */
    public function index(): JsonResponse
    {
        $offices = PassportIssuingOffice::active()
            ->orderBy('region')
            ->orderBy('name')
            ->get(['id', 'region', 'name', 'address'])
            ->groupBy('region');

        return response()->json([
            'success' => true,
            'message' => 'Passport issuing offices retrieved successfully',
            'data'    => $offices,
        ]);
    }
}