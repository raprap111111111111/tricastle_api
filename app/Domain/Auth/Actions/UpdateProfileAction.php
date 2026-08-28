<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\UpdateProfileDTO;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UpdateProfileAction
{
    public function execute(User $user, UpdateProfileDTO $data): User
    {
        $attributes = array_filter([
            'first_name'    => $data->firstName,
            'middle_name'   => $data->middleName,
            'last_name'     => $data->lastName,
            'suffix'        => $data->suffix,
            'email'         => $data->email,
            'phone'         => $data->phone,
            'mobile'        => $data->mobile,
            'date_of_birth' => $data->dateOfBirth,
            'gender'        => $data->gender,
            'department'    => $data->department,
            'position'      => $data->position,
            'address'       => $data->address,
            'city'          => $data->city,
            'province'      => $data->province,
            'country'       => $data->country,
            'postal_code'   => $data->postalCode,
            'bio'           => $data->bio,
        ], fn ($val) => $val !== null);

        // Auto-calculate full_name
        $fn = $data->firstName ?? $user->first_name;
        $mn = $data->middleName ?? $user->middle_name;
        $ln = $data->lastName ?? $user->last_name;
        $sf = $data->suffix ?? $user->suffix;
        $attributes['full_name'] = trim(implode(' ', array_filter([$fn, $mn, $ln, $sf])));

        // Manage avatar file storage
        if ($data->avatar) {
            if ($user->avatar && !str_starts_with($user->avatar, 'http') && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $attributes['avatar'] = $data->avatar->store('avatars', 'public');
        }

        $user->update($attributes);
        $user->load('roles');

        return $user->fresh();
    }
}