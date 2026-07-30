<?php

namespace App\Http\Requests\v1\ApplicantTattoo;

use App\Enums\TattooSize;
use App\Models\ApplicantTattoo;
use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantTattooRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ApplicantTattoo::class);
    }

    public function rules(): array
    {
        return [
            'applicant_id' => ['required', 'integer', 'exists:applicants,id'],
            'location'     => ['required', 'string', 'max:255'],
            'size'         => ['nullable', 'in:' . implode(',', TattooSize::values())],
            'description'  => ['nullable', 'string', 'max:5000'],

            // Photo file upload
            'photo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // max 5MB

            'is_visible'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_id.exists' => 'The selected applicant does not exist.',
            'photo.max'           => 'Photo must not be larger than 5MB.',
            'photo.mimes'         => 'Photo must be a JPG, JPEG, PNG, or WebP image.',
        ];
    }
}