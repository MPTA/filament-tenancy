<?php

namespace Database\Seeders;

use Database\Seeders\Development\AccommodationSeeder;
use Database\Seeders\Development\AttractionSeeder;
use Database\Seeders\Development\CitySeeder;
use Database\Seeders\Development\CompanionTypeSeeder;
use Database\Seeders\Development\ContactSeeder;
use Database\Seeders\Development\ExperienceSeeder;
use Database\Seeders\Development\MealTypeSeeder;
use Database\Seeders\Development\ProvinceSeeder;
use Database\Seeders\Development\QuotationSeeder;
use Database\Seeders\Development\TenantSeeder;
use Database\Seeders\Development\VehicleTypeSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * این seeder فقط برای محیط development است و شامل دیتای تستی می‌باشد.
     * 
     * ⚠️ توجه: قبل از اجرای این seeder، حتماً seedهای production را اجرا کنید:
     * php artisan db:seed --class=CurrencySeeder
     * php artisan db:seed --class=CountrySeeder
     * 
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
            ProvinceSeeder::class,
            CitySeeder::class,
            TenantSeeder::class,
            ContactSeeder::class,
            CompanionTypeSeeder::class,
            MealTypeSeeder::class,
            VehicleTypeSeeder::class,
            ExperienceSeeder::class,
            AccommodationSeeder::class,
            AttractionSeeder::class,
            QuotationSeeder::class,
        ]);

        $this->command->info('✅ Development data seeded successfully!');
    }
}
