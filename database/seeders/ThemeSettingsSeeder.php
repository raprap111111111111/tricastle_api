<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ThemeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ─── Active Theme ────────────────────────────
            [
                'key'         => 'theme.active',
                'value'       => 'default',
                'type'        => 'string',
                'group'       => 'theme',
                'description' => 'Currently active theme',
                'is_public'   => true,
            ],

            // ─── Logos ───────────────────────────────────
            [
                'key'         => 'branding.logo',
                'value'       => '/tricastle.jpg',
                'type'        => 'string',
                'group'       => 'branding',
                'description' => 'Main logo (sidebar)',
                'is_public'   => true,
            ],
            [
                'key'         => 'branding.logo_login',
                'value'       => '/tricastle.jpg',
                'type'        => 'string',
                'group'       => 'branding',
                'description' => 'Logo shown on login page',
                'is_public'   => true,
            ],
            [
                'key'         => 'branding.app_name',
                'value'       => 'Tricastle',
                'type'        => 'string',
                'group'       => 'branding',
                'description' => 'Application name',
                'is_public'   => true,
            ],
            [
                'key'         => 'branding.app_tagline',
                'value'       => 'BACOLOD',
                'type'        => 'string',
                'group'       => 'branding',
                'description' => 'Tagline under app name',
                'is_public'   => true,
            ],

            // ─── Effects ─────────────────────────────────
            [
                'key'         => 'theme.effects_enabled',
                'value'       => 'true',
                'type'        => 'boolean',
                'group'       => 'theme',
                'description' => 'Enable animated theme effects',
                'is_public'   => true,
            ],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}