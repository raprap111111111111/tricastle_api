<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Safely format avatar URL for Local, S3, or Cloudflare R2
        $avatarUrl = null;
        if ($this->avatar) {
            // Normalize Windows backslashes (avatars\file.jpg -> avatars/file.jpg)
            $path = str_replace('\\', '/', $this->avatar);

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                $avatarUrl = $path;
            } else {
                $avatarUrl = Storage::url($path);
            }
        }

        return [
            'id'                  => $this->id,
            'first_name'          => $this->first_name,
            'middle_name'         => $this->middle_name,
            'last_name'           => $this->last_name,
            'suffix'              => $this->suffix,
            'full_name'           => $this->full_name,
            'email'               => $this->email,
            'email_verified_at'   => $this->email_verified_at,
            'phone'               => $this->phone,
            'mobile'              => $this->mobile,
            'avatar'              => $avatarUrl,
            'bio'                 => $this->bio,
            'date_of_birth'       => $this->date_of_birth,
            'gender'              => $this->gender,

            // Employment
            'employee_code'       => $this->employee_code,
            'department'          => $this->department,
            'position'            => $this->position,
            'hired_date'          => $this->hired_date,
            'supervisor'          => $this->whenLoaded('supervisor', fn () => [
                'id'        => $this->supervisor?->id,
                'full_name' => $this->supervisor?->full_name,
                'email'     => $this->supervisor?->email,
            ]),

            // Address
            'address'             => $this->address,
            'city'                => $this->city,
            'province'            => $this->province,
            'country'             => $this->country ?? 'Philippines',
            'postal_code'         => $this->postal_code,

            // Status & Security
            'is_active'           => (bool) $this->is_active,
            'last_login_at'       => $this->last_login_at,
            'last_login_ip'       => $this->last_login_ip,
            'login_count'         => (int) ($this->login_count ?? 0),
            'two_factor_enabled'  => (bool) ($this->two_factor_enabled ?? false),

            // Preferences
            'theme_preference'    => $this->theme_preference ?? 'default',
            'effects_enabled'     => (bool) ($this->effects_enabled ?? true),

            // Roles & Permissions
            'roles'               => $this->whenLoaded('roles', fn() => $this->roles->pluck('name')),
            'permissions'         => $this->when(
                $this->resource->relationLoaded('roles') || true,
                fn() => $this->resource->getAllPermissions()->pluck('name')
            ),

            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
        ];
    }
}