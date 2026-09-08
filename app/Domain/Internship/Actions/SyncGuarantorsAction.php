<?php

namespace App\Domain\Internship\Actions;

use App\Domain\Internship\DTOs\SyncGuarantorsDTO;
use App\Models\Applicant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncGuarantorsAction
{
    public function execute(SyncGuarantorsDTO $dto): Collection
    {
        if (count($dto->guarantors) < 1 || count($dto->guarantors) > 2) {
            throw ValidationException::withMessages([
                'guarantors' => 'Provide 1 or 2 guarantors.',
            ]);
        }

        return DB::transaction(function () use ($dto) {
            $applicant = Applicant::findOrFail($dto->applicantId);

            foreach (array_values($dto->guarantors) as $index => $data) {
                $applicant->guarantors()->updateOrCreate(
                    ['sequence' => $index + 1],
                    [
                        'full_name'                => $data['full_name'],
                        'age'                      => $data['age'] ?? null,
                        'civil_status'             => $data['civil_status'] ?? null,
                        'nationality'              => $data['nationality'] ?? 'Filipino',
                        'address'                  => $data['address'] ?? null,
                        'residence_cert_no'        => $data['residence_cert_no'] ?? null,
                        'residence_cert_issued_at' => $data['residence_cert_issued_at'] ?? null,
                        'residence_cert_place'     => $data['residence_cert_place'] ?? null,
                        'relationship'             => $data['relationship'] ?? null,
                    ]
                );
            }

            // remove extra sequences if fewer provided
            $applicant->guarantors()
                ->where('sequence', '>', count($dto->guarantors))
                ->delete();

            return $applicant->guarantors()->orderBy('sequence')->get();
        });
    }
}