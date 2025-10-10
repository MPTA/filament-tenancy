<?php

namespace Database\Seeders;

use Database\Seeders\Development\AccommodationSeeder;
use Database\Seeders\Development\AttractionSeeder;
use Database\Seeders\Development\CitySeeder;
use Database\Seeders\Development\CountrySeeder;
use Database\Seeders\Development\CurrencySeeder;
use Database\Seeders\Development\ProvinceSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * این seeder فقط برای محیط development است و شامل دیتای تستی می‌باشد.
     * برای اجرا: php artisan db:seed --class=DevelopmentSeeder
     */
    public function run(): void
    {
        // امنیت: فقط در محیط development و local اجرا شود
        if (!app()->environment(['local', 'development'])) {
            $this->command->error('⚠️  DevelopmentSeeder can only be run in local/development environment!');
            $this->command->warn('Current environment: ' . app()->environment());
            return;
        }

        $this->command->info('🔧 Running Development Seeders...');
        
        $this->call([
            CurrencySeeder::class,
            CountrySeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
            AccommodationSeeder::class,
            AttractionSeeder::class,
        ]);

        $this->command->info('✅ Development data seeded successfully!');
    }
}
