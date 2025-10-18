<?php

namespace Database\Seeders;

use App\Models\Base\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding currencies from currencies.json...');

        $jsonPath = storage_path('data/currencies.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('❌ currencies.json file not found at: ' . $jsonPath);
            return;
        }

        $currencies = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($currencies)) {
            $this->command->error('❌ Invalid JSON format in currencies.json');
            return;
        }

        $count = 0;
        $activeCount = 0;

        foreach ($currencies as $currencyData) {
            Currency::updateOrCreate(
                ['code' => $currencyData['code']],
                [
                    'code' => $currencyData['code'],
                    'name' => ['en' => $currencyData['name']],
                    'symbol' => $currencyData['symbol'],
                    'is_active' => $currencyData['is_active'],
                ]
            );
            
            $count++;
            if ($currencyData['is_active']) {
                $activeCount++;
            }
        }

        $this->command->info("✅ Successfully seeded {$count} currencies ({$activeCount} active)");
        $this->command->line('   Active currencies: USD, CNY, EUR, GBP');
    }
}

