<?php
namespace App\Domain\ProfileUpdate\Actions;

use App\Models\ApplicantProfileUpdate;
use App\Domain\ProfileUpdate\DTOs\SubmitProfileUpdateDTO;
use Illuminate\Validation\ValidationException;

class SubmitProfileUpdateAction {
    public function execute(SubmitProfileUpdateDTO $dto): ApplicantProfileUpdate {
        $exists = ApplicantProfileUpdate::where('applicant_id', $dto->applicantId)
            ->where('status', 'pending')->exists();

        if ($exists) {
            throw ValidationException::withMessages(['general' => 'You already have a pending profile update under review by staff.']);
        }

        // Clean out null AND empty strings
        $cleanedPayload = array_filter($dto->payload, fn($val) => $val !== null && $val !== '');

        return ApplicantProfileUpdate::create([
            'applicant_id' => $dto->applicantId,
            'proposed_payload' => $cleanedPayload,
        ]);
    }
}
