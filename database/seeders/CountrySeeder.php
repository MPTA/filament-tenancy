<?php

namespace Database\Seeders;

use App\Models\Base\Country;
use App\Models\Base\Currency;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding countries from countries.json...');

        $jsonPath = storage_path('data/countries.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('❌ countries.json file not found at: ' . $jsonPath);
            return;
        }

        $countriesData = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($countriesData)) {
            $this->command->error('❌ Invalid JSON format in countries.json');
            return;
        }

        // Load all currencies for mapping
        $currencyMap = Currency::all()->keyBy('code');
        
        $this->command->info('   Loaded ' . $currencyMap->count() . ' currencies for mapping');

        $count = 0;
        $skipped = 0;

        foreach ($countriesData as $countryData) {
            // Find currency_id
            $currencyId = null;
            if (isset($countryData['currency']) && isset($currencyMap[$countryData['currency']])) {
                $currencyId = $currencyMap[$countryData['currency']]->id;
            }

            // Process translations - convert dash to underscore for Spatie translatable
            $translations = [];
            if (isset($countryData['translations']) && is_array($countryData['translations'])) {
                foreach ($countryData['translations'] as $locale => $translation) {
                    // Convert dash to underscore: zh-CN → zh_CN, pt-BR → pt_BR
                    $locale = str_replace('-', '_', $locale);
                    $translations[$locale] = $translation;
                }
            }

            // Add English name if available
            if (isset($countryData['name'])) {
                $translations['en'] = $countryData['name'];
            }

            try {
                Country::updateOrCreate(
                    ['code' => $countryData['iso2']],
                    [
                        'code' => $countryData['iso2'],
                        'name' => $translations,
                        'currency_id' => $currencyId,
                        'iso3' => $countryData['iso3'] ?? null,
                        'numeric_code' => $countryData['numeric_code'] ?? null,
                        'phone_code' => $countryData['phonecode'] ?? null,
                        'capital' => $countryData['capital'] ?? null,
                        'tld' => $countryData['tld'] ?? null,
                        'native_name' => $countryData['native'] ?? null,
                        'population' => $countryData['population'] ?? null,
                        'gdp' => $countryData['gdp'] ?? null,
                        'nationality' => $countryData['nationality'] ?? null,
                        'timezones' => $countryData['timezones'] ?? null,
                        'latitude' => $countryData['latitude'] ?? null,
                        'longitude' => $countryData['longitude'] ?? null,
                        'emoji' => $countryData['emoji'] ?? null,
                        'emoji_u' => $countryData['emojiU'] ?? null,
                        'wiki_data_id' => $countryData['wikiDataId'] ?? null,
                    ]
                );
                
                $count++;
            } catch (\Exception $e) {
                $this->command->warn("   ⚠️  Skipped {$countryData['iso2']}: " . $e->getMessage());
                $skipped++;
            }
        }

        $this->command->info("✅ Successfully seeded {$count} countries");
        if ($skipped > 0) {
            $this->command->warn("   ⚠️  Skipped {$skipped} countries due to errors");
        }
    }
}

