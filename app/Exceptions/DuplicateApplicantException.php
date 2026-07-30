<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DuplicateApplicantException extends Exception
{
    public function __construct(
        public readonly array $duplicates = [],
        string $message = 'Duplicate applicant detected.',
        int $code = 422,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success'    => false,
            'message'    => $this->getMessage(),
            'duplicates' => $this->duplicates,
        ], 422);
    }

    /**
     * Report the exception (log it).
     */
    public function report(): bool
    {
        // Return false so it uses the default logger,
        // or return true to skip logging (we log manually in the action)
        return true;
    }
}