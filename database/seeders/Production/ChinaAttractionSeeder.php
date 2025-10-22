<?php

namespace Database\Seeders\Production;

use App\Enums\AttractionTypeEnum;
use App\Models\Base\Attraction;
use App\Models\Base\City;
use App\Models\Base\Country;
use App\Models\Base\SubAttraction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ChinaAttractionSeeder extends Seeder
{
    /**
     * City code mapping for each JSON file
     */
    private array $cityMapping = [
        'beijing' => 'BJS',
        'chengdu' => 'CTU',
        'guangzhou' => 'CAN',
        'guilin' => 'KWL',
        'haikou' => 'HAK',
        'hangzhou' => 'HGH',
        'kunming' => 'KMG',
        'nanjing' => 'NKG',
        'sanya' => 'SYX',
        'shanghai' => 'SHA',
        'shenzhen' => 'SZX',
        'suzhou' => 'SZV',
        'xian' => null, // Special case: search by name
    ];

    private array $processedNames = []; // Track duplicate names per city (city_id|name)
    private int $totalAttractions = 0;
    private int $totalSubAttractions = 0;

    public function run(): void
    {
        $this->command->info('🏛️ Seeding China attractions from JSON files...');

        $china = Country::where('code', 'CN')->first();
        if (!$china) {
            throw new \Exception('China country (code: CN) not found. Please run CountrySeeder first.');
        }

        $attractionsPath = storage_path('data/china-attractions');
        $jsonFiles = File::glob($attractionsPath . '/*.json');

        foreach ($jsonFiles as $filePath) {
            $filename = basename($filePath, '.json');

            // Skip if not in our city mapping
            if (!array_key_exists($filename, $this->cityMapping)) {
                $this->command->warn("   ⏭️ Skipping {$filename}.json (not in city mapping)");
                continue;
            }

            $this->command->info("📄 Processing: {$filename}.json");

            // Get city
            $city = $this->getCityForFile($filename);
            if (!$city) {
                throw new \Exception("City not found for file: {$filename}.json");
            }

            // Read and parse JSON
            $jsonContent = File::get($filePath);
            $attractions = json_decode($jsonContent, true);

            if (!is_array($attractions)) {
                throw new \Exception("Invalid JSON format in file: {$filename}.json");
            }

            $fileAttractionCount = 0;
            $fileSubAttractionCount = 0;

            foreach ($attractions as $index => $attraction) {
                // Validate required fields
                if (empty($attraction['name']) || (empty($attraction['name']['en']) && empty($attraction['name']['zh_CN']))) {
                    throw new \Exception("Missing name for attraction at index {$index} in {$filename}.json");
                }

                // Check for duplicate names within the same city (using city_id + name as unique key)
                $attractionNameEn = $attraction['name']['en'] ?? $attraction['name']['zh_CN'] ?? null;
                $uniqueKey = $city->id . '|' . $attractionNameEn; // Combine city_id + name
                
                if ($attractionNameEn && isset($this->processedNames[$uniqueKey])) {
                    throw new \Exception(
                        "Duplicate attraction name '{$attractionNameEn}' found in {$filename}.json for city {$city->name}. " .
                        "This attraction already exists in the same city."
                    );
                }

                // Prepare name (only include languages that exist)
                $name = [];
                if (!empty($attraction['name']['en'])) {
                    $name['en'] = $attraction['name']['en'];
                }
                if (!empty($attraction['name']['zh_CN'])) {
                    $name['zh_CN'] = $attraction['name']['zh_CN'];
                }

                // Prepare description
                $description = null;
                if (!empty($attraction['description'])) {
                    $description = [];
                    if (!empty($attraction['description']['en'])) {
                        $description['en'] = $attraction['description']['en'];
                    }
                    if (!empty($attraction['description']['zh_CN'])) {
                        $description['zh_CN'] = $attraction['description']['zh_CN'];
                    }
                    if (empty($description)) {
                        $description = null;
                    }
                }

                // Prepare opening_hours
                $openingHours = null;
                if (!empty($attraction['opening_hours'])) {
                    $openingHours = [];
                    if (!empty($attraction['opening_hours']['en'])) {
                        $openingHours['en'] = $attraction['opening_hours']['en'];
                    }
                    if (!empty($attraction['opening_hours']['zh_CN'])) {
                        $openingHours['zh_CN'] = $attraction['opening_hours']['zh_CN'];
                    }
                    if (empty($openingHours)) {
                        $openingHours = null;
                    }
                }

                // Prepare suggested_duration
                $suggestedDuration = null;
                if (!empty($attraction['suggested_duration'])) {
                    $suggestedDuration = [];
                    if (!empty($attraction['suggested_duration']['en'])) {
                        $suggestedDuration['en'] = $attraction['suggested_duration']['en'];
                    }
                    if (!empty($attraction['suggested_duration']['zh_CN'])) {
                        $suggestedDuration['zh_CN'] = $attraction['suggested_duration']['zh_CN'];
                    }
                    if (empty($suggestedDuration)) {
                        $suggestedDuration = null;
                    }
                }

                // Prepare suggested_season
                $suggestedSeason = null;
                if (!empty($attraction['suggested_season'])) {
                    $suggestedSeason = [];
                    if (!empty($attraction['suggested_season']['en'])) {
                        $suggestedSeason['en'] = $attraction['suggested_season']['en'];
                    }
                    if (!empty($attraction['suggested_season']['zh_CN'])) {
                        $suggestedSeason['zh_CN'] = $attraction['suggested_season']['zh_CN'];
                    }
                    if (empty($suggestedSeason)) {
                        $suggestedSeason = null;
                    }
                }

                // Prepare ticket_info
                $ticketInfo = null;
                if (!empty($attraction['ticket_info'])) {
                    $ticketInfo = [];
                    if (!empty($attraction['ticket_info']['en'])) {
                        $ticketInfo['en'] = $attraction['ticket_info']['en'];
                    }
                    if (!empty($attraction['ticket_info']['zh_CN'])) {
                        $ticketInfo['zh_CN'] = $attraction['ticket_info']['zh_CN'];
                    }
                    if (empty($ticketInfo)) {
                        $ticketInfo = null;
                    }
                }

                // Prepare tips
                $tips = null;
                if (!empty($attraction['tips'])) {
                    $tips = [];
                    if (!empty($attraction['tips']['en'])) {
                        $tips['en'] = $attraction['tips']['en'];
                    }
                    if (!empty($attraction['tips']['zh_CN'])) {
                        $tips['zh_CN'] = $attraction['tips']['zh_CN'];
                    }
                    if (empty($tips)) {
                        $tips = null;
                    }
                }

                // Prepare duration_hours (not translatable, just JSON object)
                $durationHours = null;
                if (!empty($attraction['duration_hours'])) {
                    $durationHours = $attraction['duration_hours'];
                }

                // Get type from JSON or default to uncategorized
                $type = AttractionTypeEnum::UNCATEGORIZED;
                if (!empty($attraction['type'])) {
                    $type = AttractionTypeEnum::tryFrom($attraction['type']) ?? AttractionTypeEnum::UNCATEGORIZED;
                }

                // Create attraction
                $attractionModel = Attraction::create([
                    'name' => $name,
                    'description' => $description,
                    'type' => $type,
                    'link' => $attraction['link'] ?? null,
                    'address' => $attraction['address'] ?? null,
                    'opening_hours' => $openingHours,
                    'image_url' => $attraction['image_url'] ?? null,
                    'rating' => !empty($attraction['rating']) ? (float) $attraction['rating'] : null,
                    'suggested_duration' => $suggestedDuration,
                    'suggested_season' => $suggestedSeason,
                    'ticket_info' => $ticketInfo,
                    'tips' => $tips,
                    'duration_hours' => $durationHours,
                    'local_price' => $attraction['local_price'] ?? null,
                    'foreigner_price' => null, // Not in JSON
                    'season_spring' => $attraction['season_spring'] ?? false,
                    'season_summer' => $attraction['season_summer'] ?? false,
                    'season_autumn' => $attraction['season_autumn'] ?? false,
                    'season_winter' => $attraction['season_winter'] ?? false,
                    'country_id' => $china->id,
                    'city_id' => $city->id,
                    'district_id' => null, // Not in JSON
                    'is_active' => true,
                ]);

                // Mark name as processed for this city
                if ($attractionNameEn) {
                    $this->processedNames[$uniqueKey] = "{$filename}.json";
                }

                $fileAttractionCount++;

                // Process sub_attractions
                if (!empty($attraction['sub_attractions']) && is_array($attraction['sub_attractions'])) {
                    foreach ($attraction['sub_attractions'] as $subAttractionData) {
                        if (empty($subAttractionData['name'])) {
                            continue; // Skip if no name
                        }

                        // Prepare sub-attraction name
                        $subName = [];
                        if (!empty($subAttractionData['name']['en'])) {
                            $subName['en'] = $subAttractionData['name']['en'];
                        }
                        if (!empty($subAttractionData['name']['zh_CN'])) {
                            $subName['zh_CN'] = $subAttractionData['name']['zh_CN'];
                        }

                        if (empty($subName)) {
                            continue; // Skip if no valid name
                        }

                        SubAttraction::create([
                            'name' => $subName,
                            'description' => null, // Not in JSON
                            'attraction_id' => $attractionModel->id,
                            'latitude' => null, // Not in JSON
                            'longitude' => null, // Not in JSON
                            'local_price' => $subAttractionData['local_price'] ?? null,
                            'foreigner_price' => null, // Not in JSON
                        ]);

                        $fileSubAttractionCount++;
                    }
                }
            }

            $this->command->info("   ✓ Imported {$fileAttractionCount} attractions and {$fileSubAttractionCount} sub-attractions");
            $this->totalAttractions += $fileAttractionCount;
            $this->totalSubAttractions += $fileSubAttractionCount;
        }

        $this->command->newLine();
        $this->command->info("✅ Successfully imported {$this->totalAttractions} attractions and {$this->totalSubAttractions} sub-attractions from " . count($jsonFiles) . " cities");
    }

    /**
     * Get city for a given filename
     */
    private function getCityForFile(string $filename): ?City
    {
        $cityCode = $this->cityMapping[$filename];

        // Special case for Xian (no IATA code)
        if ($filename === 'xian') {
            return City::where('name->en', 'Xian')->first();
        }

        // For other cities, search by code
        return City::where('code', $cityCode)->first();
    }
}

