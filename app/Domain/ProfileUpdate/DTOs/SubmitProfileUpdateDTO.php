<?php
namespace App\Domain\ProfileUpdate\DTOs;

readonly class SubmitProfileUpdateDTO {
    public function __construct(
        public int $applicantId,
        public array $payload
    ) {}
}
