<?php

// app/Domain/ApplicantTattoo/Mappers/ApplicantTattooMapper.php

namespace App\Domain\ApplicantTattoo\Mappers;

use App\Domain\ApplicantTattoo\DTOs\CreateApplicantTattooDTO;
use App\Domain\ApplicantTattoo\DTOs\UpdateApplicantTattooDTO;
use App\Http\Requests\v1\ApplicantTattoo\StoreApplicantTattooRequest;
use App\Http\Requests\v1\ApplicantTattoo\UpdateApplicantTattooRequest;

class ApplicantTattooMapper
{
    public static function fromStoreRequest(StoreApplicantTattooRequest $request): CreateApplicantTattooDTO
    {
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('applicant-tattoos', 'public');
        }

        return new CreateApplicantTattooDTO(
            applicantId: (int) $request->validated('applicant_id'),
            location:    $request->validated('location'),
            size:        $request->validated('size'),
            description: $request->validated('description'),
            photoPath:   $photoPath,
            isVisible:   (bool) $request->validated('is_visible', true),
        );
    }

    public static function fromUpdateRequest(UpdateApplicantTattooRequest $request): UpdateApplicantTattooDTO
    {
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('applicant-tattoos', 'public');
        }

        return new UpdateApplicantTattooDTO(
            location:    $request->validated('location'),
            size:        $request->validated('size'),
            description: $request->validated('description'),
            photoPath:   $photoPath,
            isVisible:   $request->has('is_visible')
                            ? (bool) $request->validated('is_visible')
                            : null,
        );
    }
}