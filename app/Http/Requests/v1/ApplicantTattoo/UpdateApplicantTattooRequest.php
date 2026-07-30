<?php

namespace App\Http\Requests\v1\ApplicantTattoo;

use App\Enums\TattooSize;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantTattooRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'update',
            $this->route('applicant_tattoo')
        );
    }

    public function rules(): array
    {
        return [
            'location'    => ['sometimes', 'required', 'string', 'max:255'],
            'size'        => ['sometimes', 'nullable', 'in:' . implode(',', TattooSize::values())],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],

            'photo'       => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'is_visible'  => ['sometimes', 'boolean'],
        ];
    }
}