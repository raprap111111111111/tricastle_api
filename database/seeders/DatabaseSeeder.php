<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════╗');
        $this->command->info('║   🚀 TriCastle Database Seeder           ║');
        $this->command->info('╚══════════════════════════════════════════╝');
        $this->command->info('');

        // ⚠️ ORDER MATTERS!
        $this->call([
            PermissionsSeeder::class,   
            RolesSeeder::class,         
            UsersSeeder::class,     
            ApplicantSeeder::class,
            DocumentTypeSeeder::class,
            ThemeSettingsSeeder::class,
            LegacyApplicantsSeeder::class,

        ]);

        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════╗');
        $this->command->info('║   ✅ All Seeders Completed!              ║');
        $this->command->info('╚══════════════════════════════════════════╝');
        $this->command->info('');
    }
}