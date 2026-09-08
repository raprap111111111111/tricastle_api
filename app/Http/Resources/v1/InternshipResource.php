<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\v1\InternshipDocumentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InternshipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'applicant_id'           => $this->applicant_id,
            'batch_id'               => $this->batch_id,
            'internship_program_id'  => $this->internship_program_id,
            'program_type'           => $this->program_type?->value ?? $this->program_type,
            'status'                 => $this->status?->value ?? $this->status,
            'status_label'           => $this->status?->label(),
            'is_current'             => $this->is_current,
            'job_description'        => $this->job_description,
            'place_of_internship'    => $this->place_of_internship,
            'municipality'           => $this->municipality,
            'agreement_date'         => $this->agreement_date?->toDateString(),
            'contract_start'         => $this->contract_start?->toDateString(),
            'contract_end'           => $this->contract_end?->toDateString(),
            'contract_years'         => $this->contract_years,
            'stipend_amount'         => $this->stipend_amount,
            'meal_allowance_amount'  => $this->meal_allowance_amount,
            'work_days'              => $this->work_days,
            'day_off'                => $this->day_off,
            'time_start'             => $this->time_start,
            'time_end'               => $this->time_end,
            'lunch_break'            => $this->lunch_break,
            'change_reason'          => $this->change_reason,
            'changed_at'             => $this->changed_at?->toISOString(),
            'can_change_company'     => $this->canChangeCompany(),
            'previous_internship_id' => $this->previous_internship_id,

            'applicant' => $this->whenLoaded('applicant', fn () => [
                'id'             => $this->applicant->id,
                'applicant_code' => $this->applicant->applicant_code,
                'full_name'      => $this->applicant->full_name,
                'passport_number'=> $this->applicant->passport_number,
            ]),

            'program' => $this->whenLoaded('program', fn () => new InternshipProgramResource($this->program)),
            'batch'   => $this->whenLoaded('batch', fn () => [
                'id' => $this->batch?->id,
                'batch_number' => $this->batch?->batch_number,
                'name' => $this->batch?->name,
            ]),

            'dispatching_company' => $this->whenLoaded('dispatchingCompany', fn () => [
                'id' => $this->dispatchingCompany?->id,
                'code' => $this->dispatchingCompany?->code,
                'name' => $this->dispatchingCompany?->name,
            ]),
            'accepting_company' => $this->whenLoaded('acceptingCompany', fn () => [
                'id' => $this->acceptingCompany?->id,
                'code' => $this->acceptingCompany?->code,
                'name' => $this->acceptingCompany?->name,
            ]),
            'receiving_company' => $this->whenLoaded('receivingCompany', fn () => [
                'id' => $this->receivingCompany?->id,
                'code' => $this->receivingCompany?->code,
                'name' => $this->receivingCompany?->name,
            ]),

            'documents' => InternshipDocumentResource::collection($this->whenLoaded('documents')),
            'created_at'=> $this->created_at?->toISOString(),
            'updated_at'=> $this->updated_at?->toISOString(),
        ];
    }
}