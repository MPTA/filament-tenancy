<?php

namespace Database\Seeders;

use App\Models\Base\Country;
use App\Models\Base\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds all China provinces from china-provinces.json
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding China provinces from china-provinces.json...');

        $jsonPath = storage_path('data/china-provinces.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('❌ china-provinces.json file not found at: ' . $jsonPath);
            return;
        }

        // Get China country
        $china = Country::where('code', 'CN')->first();
        
        if (!$china) {
            $this->command->error('❌ China country not found. Please run CountrySeeder first.');
            return;
        }

        $provincesData = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($provincesData)) {
            $this->command->error('❌ Invalid JSON format in china-provinces.json');
            return;
        }

        $count = 0;
        $skipped = 0;

        foreach ($provincesData as $provinceData) {
            // Process translations - convert dash to underscore for Spatie translatable
            $translations = [];
            if (isset($provinceData['translations']) && is_array($provinceData['translations'])) {
                foreach ($provinceData['translations'] as $locale => $translation) {
                    // Convert dash to underscore: pt-BR → pt_BR
                    $locale = str_replace('-', '_', $locale);
                    $translations[$locale] = $translation;
                }
            }

            // Add English name if available
            if (isset($provinceData['name'])) {
                $translations['en'] = $provinceData['name'];
            }

            try {
                Province::updateOrCreate(
                    [
                        'country_id' => $china->id,
                        'code' => $provinceData['iso2']
                    ],
                    [
                        'country_id' => $china->id,
                        'code' => $provinceData['iso2'],
                        'name' => $translations,
                        'iso3166_2' => $provinceData['iso3166_2'] ?? null,
                        'fips_code' => $provinceData['fips_code'] ?? null,
                        'level' => $provinceData['level'] ?? null,
                        'latitude' => $provinceData['latitude'] ?? null,
                        'longitude' => $provinceData['longitude'] ?? null,
                        'timezone' => $provinceData['timezone'] ?? null,
                        'wiki_data_id' => $provinceData['wikiDataId'] ?? null,
                    ]
                );
                
                $count++;
            } catch (\Exception $e) {
                $this->command->warn("   ⚠️  Skipped {$provinceData['iso2']}: " . $e->getMessage());
                $skipped++;
            }
        }

        $this->command->info("✅ Successfully seeded {$count} China provinces");
        if ($skipped > 0) {
            $this->command->warn("   ⚠️  Skipped {$skipped} provinces due to errors");
        }
        
        // Show key provinces for development
        $keyProvinces = Province::whereIn('code', ['BJ', 'SH', 'GD'])->get(['code', 'name']);
        if ($keyProvinces->count() > 0) {
            $this->command->line('   Key provinces for development:');
            foreach ($keyProvinces as $p) {
                $this->command->line("     - {$p->code}: " . $p->getTranslation('name', 'en'));
            }
        }
    }
}

