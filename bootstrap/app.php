<?php

use App\Exceptions\DuplicateApplicantException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__.'/../routes/web.php',
        api:      __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ✅ Return JSON 401 instead of redirecting to 'login' route
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }
            return '/login';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $e, Request $request) {

            if (! ($request->is('api/*') || $request->expectsJson())) {
                return null;
            }

            // ─── 422 Duplicate Applicant (custom) ─────────────────────
            if ($e instanceof DuplicateApplicantException) {
                return response()->json([
                    'success'    => false,
                    'message'    => $e->getMessage(),
                    'duplicates' => $e->duplicates,
                ], 422);
            }

            // ─── 401 Unauthenticated ──────────────────────────────────
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login first.',
                ], 401);
            }

            // ─── 403 Unauthorized ─────────────────────────────────────
            if ($e instanceof AuthorizationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not have permission to perform this action.',
                ], 403);
            }

            // ─── 404 Model Not Found ──────────────────────────────────
            if ($e instanceof ModelNotFoundException) {
                $model = class_basename($e->getModel());

                return response()->json([
                    'success' => false,
                    'message' => "{$model} not found.",
                ], 404);
            }

            // ─── 404 Route Not Found ──────────────────────────────────
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Route not found.',
                ], 404);
            }

            // ─── 405 Method Not Allowed ───────────────────────────────
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed.',
                ], 405);
            }

            // ─── 422 Validation ───────────────────────────────────────
            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $e->errors(),
                ], 422);
            }

            // ─── 500 Server Error ─────────────────────────────────────
            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again later.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'file'    => config('app.debug') ? str_replace(base_path(), '', $e->getFile()) : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
            ], 500);
        });
    })
    ->create();