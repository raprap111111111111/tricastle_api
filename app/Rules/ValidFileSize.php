<?php

// app/Rules/ValidFileSize.php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFileSize implements ValidationRule
{
    private int $maxSizeKb;

    public function __construct(?int $maxSizeKb = null)
    {
        $this->maxSizeKb = $maxSizeKb ?? config('upload.max_size', 10240);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value || ! method_exists($value, 'getSize')) {
            $fail("The {$attribute} must be a valid file.");
            return;
        }

        $fileSizeKb = $value->getSize() / 1024;

        if ($fileSizeKb > $this->maxSizeKb) {
            $maxMb = round($this->maxSizeKb / 1024, 2);
            $fail("The {$attribute} must not exceed {$maxMb}MB.");
        }
    }
}
