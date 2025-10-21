<?php

namespace Database\Seeders\Production;

use App\Models\Base\Accommodation;
use App\Models\Base\City;
use App\Models\Base\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ChinaAccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏨 Seeding China accommodations from JSON files...');
        $this->command->newLine();

        // Get China country
        $china = Country::where('code', 'CN')->first();
        if (!$china) {
            throw new \Exception('China country (code: CN) not found. Please run CountrySeeder first.');
        }

        // No need to cache cities - we'll query directly for accuracy

        // Get all hotel JSON files
        $hotelFilesPath = storage_path('data/china-hotels');
        $jsonFiles = File::glob($hotelFilesPath . '/*.json');

        if (empty($jsonFiles)) {
            throw new \Exception("No JSON files found in {$hotelFilesPath}");
        }

        $totalHotels = 0;
        $processedPinCodes = [];

        foreach ($jsonFiles as $filePath) {
            $filename = basename($filePath, '.json');
            $this->command->info("📄 Processing: {$filename}");

            $jsonContent = File::get($filePath);
            $hotels = json_decode($jsonContent, true);

            if (!is_array($hotels)) {
                throw new \Exception("Invalid JSON format in file: {$filename}");
            }

            $fileHotelCount = 0;

            foreach ($hotels as $index => $hotel) {
                // Validate required fields
                if (empty($hotel['cityName'])) {
                    throw new \Exception("Missing cityName in {$filename} at index {$index}");
                }

                if (empty($hotel['HotelCode'])) {
                    throw new \Exception("Missing HotelCode for hotel in {$filename} at index {$index}");
                }

                // Check for duplicate HotelCode
                if (isset($processedPinCodes[$hotel['HotelCode']])) {
                    throw new \Exception(
                        "Duplicate HotelCode '{$hotel['HotelCode']}' found in {$filename}. " .
                        "Previously seen in {$processedPinCodes[$hotel['HotelCode']]}"
                    );
                }

                // Find city by name (English) using Spatie Translatable
                $cityName = $hotel['cityName'];
                $city = City::where('name->en', $cityName)->first();

                if (!$city) {
                    throw new \Exception(
                        "City '{$cityName}' not found in database. " .
                        "File: {$filename}, Hotel: " . ($hotel['HotelName']['en'] ?? 'Unknown')
                    );
                }

                // Parse facilities
                $facilities = null;
                if (!empty($hotel['HotelFacilities'])) {
                    // Split by space and filter empty values
                    $facilitiesArray = array_values(array_filter(
                        explode(' ', $hotel['HotelFacilities']),
                        fn($item) => !empty(trim($item))
                    ));
                    $facilities = ['en' => $facilitiesArray];
                }

                // Prepare hotel name - handle both array and string formats
                $name = [];
                if (isset($hotel['HotelName'])) {
                    if (is_array($hotel['HotelName'])) {
                        // HotelName is an object with en and zh_CN
                        if (!empty($hotel['HotelName']['en'])) {
                            $name['en'] = $hotel['HotelName']['en'];
                        }
                        if (!empty($hotel['HotelName']['zh_CN'])) {
                            $name['zh_CN'] = $hotel['HotelName']['zh_CN'];
                        }
                    } elseif (is_string($hotel['HotelName']) && !empty($hotel['HotelName'])) {
                        // HotelName is a simple string - use as English name
                        $name['en'] = $hotel['HotelName'];
                    }
                }

                // Skip if no name at all
                if (empty($name)) {
                    $hotelCode = $hotel['HotelCode'] ?? 'Unknown';
                    $this->command->warn("   ⚠ Skipping hotel {$hotelCode} with no name at index {$index} in {$filename}");
                    continue;
                }

                // Create accommodation
                Accommodation::create([
                    'name' => $name,
                    'content' => null, // Not in JSON
                    'star_rating' => $hotel['star_rating'] ?? null,
                    'address' => $hotel['Address'] ?? null,
                    'latitude' => $hotel['latitude'] ?? null,
                    'longitude' => $hotel['longitude'] ?? null,
                    'country_id' => $china->id,
                    'city_id' => $city->id,
                    'district_id' => null, // Not in JSON
                    'external_id' => $hotel['HotelCode'],
                    'external_dataset' => $filename,
                    'attractions_data' => !empty($hotel['Attractions']) ? ['en' => $hotel['Attractions']] : null,
                    'description' => !empty($hotel['Description']) ? ['en' => $hotel['Description']] : null,
                    'phone_number' => $hotel['PhoneNumber'] ?? null,
                    'website' => $hotel['HotelWebsiteUrl'] ?? null,
                    'facilities' => $facilities,
                    'is_active' => true,
                ]);

                // Mark HotelCode as processed
                $processedPinCodes[$hotel['HotelCode']] = $filename;
                $fileHotelCount++;
            }

            $this->command->info("   ✓ Imported {$fileHotelCount} hotels");
            $totalHotels += $fileHotelCount;
        }

        $this->command->newLine();
        $this->command->info("✅ Successfully imported {$totalHotels} hotels from " . count($jsonFiles) . " files");
    }
}

