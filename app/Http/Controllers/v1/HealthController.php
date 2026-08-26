<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Handle keep-alive health check for Render & Aiven MySQL.
     */
    public function __invoke(): JsonResponse
    {
        $dbStatus = 'ok';

        try {
            // 🎯 1ms DB ping so Aiven stays awake
            DB::select('SELECT 1');
        } catch (\Throwable $e) {
            $dbStatus = 'disconnected';
        }

        return response()->json([
            'status'    => 'ok',
            'database'  => $dbStatus,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}