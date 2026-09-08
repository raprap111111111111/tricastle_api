<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'middle_name'       => $this->middle_name,
            'last_name'         => $this->last_name,
            'suffix'            => $this->suffix,
            'full_name'         => $this->full_name,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'phone'             => $this->phone,
            'mobile'            => $this->mobile,
            'avatar'            => $this->resolveAvatarUrl(),
            'bio'               => $this->bio,
            'date_of_birth'     => $this->date_of_birth,
            'gender'            => $this->gender,

            // Employment
            'employee_code' => $this->employee_code,
            'department'    => $this->department,
            'position'      => $this->position,
            'hired_date'    => $this->hired_date,
            'supervisor'    => $this->whenLoaded('supervisor', fn() => [
                'id'        => $this->supervisor?->id,
                'full_name' => $this->supervisor?->full_name,
                'email'     => $this->supervisor?->email,
            ]),

            // Address
            'address'     => $this->address,
            'city'        => $this->city,
            'province'    => $this->province,
            'country'     => $this->country ?? 'Philippines',
            'postal_code' => $this->postal_code,

            // Status & Security
            'is_active'          => (bool) $this->is_active,
            'last_login_at'      => $this->last_login_at,
            'last_login_ip'      => $this->last_login_ip,
            'login_count'        => (int) ($this->login_count ?? 0),
            'two_factor_enabled' => (bool) ($this->two_factor_enabled ?? false),

            // Preferences
            'theme_preference' => $this->theme_preference ?? 'default',
            'effects_enabled'  => (bool) ($this->effects_enabled ?? true),

            // Roles & Permissions
            'roles' => $this->whenLoaded('roles', fn() => $this->roles->pluck('name')),
            'permissions' => $this->when(
                $this->relationLoaded('roles'),
                fn() => $this->resource->getAllPermissions()->pluck('name')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Avatar URL resolver:
     * - external OAuth URLs kept as-is
     * - stored paths served via API stream (works with R2 + VPN)
     */
    private function resolveAvatarUrl(): ?string
    {
        $raw = $this->resource->getRawOriginal('avatar') ?? null;

        if (empty($raw)) {
            return null;
        }

        $path = str_replace('\\', '/', (string) $raw);

        // Google/OAuth/etc. full URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $this->forceHttpsIfNeeded($path);
        }

        // Stream through backend — do NOT return r2.cloudflarestorage.com direct URL
        return $this->forceHttpsIfNeeded(
            url("/api/v1/users/{$this->id}/avatar")
        );
    }

    private function forceHttpsIfNeeded(?string $url): ?string
    {
        if (
            $url
            && str_starts_with($url, 'http://')
            && ! str_contains($url, 'localhost')
            && ! str_contains($url, '127.0.0.1')
        ) {
            return str_replace('http://', 'https://', $url);
        }

        return $url;
    }
}
