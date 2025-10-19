<?php

namespace Database\Seeders;

use App\Models\Base\City;
use App\Models\Base\Province;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds all China cities from china-cities.json
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding China cities from china-cities.json...');

        $jsonPath = storage_path('data/china-cities.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('❌ china-cities.json file not found at: ' . $jsonPath);
            return;
        }

        $citiesData = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($citiesData)) {
            $this->command->error('❌ Invalid JSON format in china-cities.json');
            return;
        }

        // Load all provinces for mapping by state_code
        $provinceMap = Province::all()->keyBy('code');
        
        $this->command->info('   Loaded ' . $provinceMap->count() . ' provinces for mapping');

        $count = 0;
        $skipped = 0;

        foreach ($citiesData as $cityData) {
            // Find province_id by state_code
            $provinceId = null;
            if (isset($cityData['state_code']) && isset($provinceMap[$cityData['state_code']])) {
                $provinceId = $provinceMap[$cityData['state_code']]->id;
            }

            if (!$provinceId) {
                $this->command->warn("   ⚠️  Skipped {$cityData['name']}: Province not found for state_code {$cityData['state_code']}");
                $skipped++;
                continue;
            }

            // Process translations - convert dash to underscore for Spatie translatable
            $translations = [];
            if (isset($cityData['translations']) && is_array($cityData['translations'])) {
                foreach ($cityData['translations'] as $locale => $translation) {
                    // Convert dash to underscore: pt-BR → pt_BR, zh-CN → zh_CN
                    $locale = str_replace('-', '_', $locale);
                    $translations[$locale] = $translation;
                }
            }

            // Add English name if available
            if (isset($cityData['name'])) {
                $translations['en'] = $cityData['name'];
            }

            // Use code from JSON (IATA code if available, otherwise city name)
            $code = $cityData['code'] ?? $cityData['name'];
            
            // Check if city has real IATA code (code exists and is different from name)
            $hasCode = isset($cityData['code']) && $cityData['code'] !== $cityData['name'];

            try {
                City::updateOrCreate(
                    [
                        'province_id' => $provinceId,
                        'code' => $code
                    ],
                    [
                        'province_id' => $provinceId,
                        'code' => $code,
                        'has_code' => $hasCode,
                        'name' => $translations,
                        'latitude' => $cityData['latitude'] ?? null,
                        'longitude' => $cityData['longitude'] ?? null,
                        'native' => $cityData['native'] ?? null,
                        'timezone' => $cityData['timezone'] ?? null,
                        'wiki_data_id' => $cityData['wikiDataId'] ?? null,
                    ]
                );
                
                $count++;
            } catch (\Exception $e) {
                $this->command->warn("   ⚠️  Skipped {$cityData['name']}: " . $e->getMessage());
                $skipped++;
            }
        }

        $this->command->info("✅ Successfully seeded {$count} China cities");
        if ($skipped > 0) {
            $this->command->warn("   ⚠️  Skipped {$skipped} cities due to errors");
        }
        
        // Show statistics about IATA codes
        $citiesWithCode = City::where('has_code', true)->count();
        $citiesWithoutCode = City::where('has_code', false)->count();
        $this->command->line("   📊 Cities with IATA code: {$citiesWithCode}");
        $this->command->line("   📊 Cities without IATA code: {$citiesWithoutCode}");
        
        // Show key cities for development
        $keyCities = City::whereIn('code', ['BJS', 'SHA', 'SZX'])->get(['code', 'name', 'has_code']);
        if ($keyCities->count() > 0) {
            $this->command->line('   Key cities for development:');
            foreach ($keyCities as $c) {
                $hasCodeIcon = $c->has_code ? '✓' : '✗';
                $this->command->line("     - {$c->code}: " . $c->getTranslation('name', 'en') . " [{$hasCodeIcon}]");
            }
        }
    }
}

