<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds all production data in the correct order:
     * 1. Currencies
     * 2. Countries
     * 3. Provinces
     * 4. Cities
     */
    public function run(): void
    {
        $this->command->info('🌍 Seeding Production Data...');
        $this->command->newLine();

        $this->call([
            CurrencySeeder::class,
            CountrySeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Production data seeded successfully!');
        $this->command->newLine();
    }
}
