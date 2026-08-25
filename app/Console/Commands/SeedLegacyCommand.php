<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedLegacyCommand extends Command
{
    protected $signature = 'seed:legacy';
    protected $description = 'Seed legacy CSV applicants safely';

    public function handle()
    {
        $this->info('🚀 Starting fast legacy CSV seed...');
        
        Artisan::call('db:seed', [
            '--class' => 'LegacyApplicantsSeeder',
            '--force' => true,
        ]);

        $this->info(Artisan::output());
        $this->info('✅ Legacy applicants seeded successfully!');
        return 0;
    }
}