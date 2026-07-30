<?php

// app/Rules/ValidFileType.php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFileType implements ValidationRule
{
    private array $allowedTypes;

    public function __construct(?array $allowedTypes = null)
    {
        $this->allowedTypes = $allowedTypes ?? array_merge(
            config('upload.allowed_mimes.image', []),
            config('upload.allowed_mimes.document', []),
        );
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value || ! method_exists($value, 'getClientOriginalExtension')) {
            $fail("The {$attribute} must be a valid file.");
            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());

        if (! in_array($extension, $this->allowedTypes)) {
            $allowed = implode(', ', $this->allowedTypes);
            $fail("The {$attribute} must be one of: {$allowed}.");
        }
    }
}
