<?php

namespace Database\Seeders;

use App\Models\Base\VehicleCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleCategories = [
            [
                'slug' => 'sedan',
                'name' => ['en' => 'Sedan'],
                'description' => ['en' => 'Standard 4-passenger sedan'],
                'is_active' => true,
            ],
            [
                'slug' => 'suv',
                'name' => ['en' => 'SUV'],
                'description' => ['en' => '5-passenger SUV'],
                'is_active' => true,
            ],
            [
                'slug' => 'minivan',
                'name' => ['en' => 'Minivan'],
                'description' => ['en' => '7-passenger minivan'],
                'is_active' => true,
            ],
            [
                'slug' => 'van',
                'name' => ['en' => 'Van'],
                'description' => ['en' => '12-passenger van'],
                'is_active' => true,
            ],
            [
                'slug' => 'minibus',
                'name' => ['en' => 'Minibus'],
                'description' => ['en' => 'Standard 20-passenger minibus'],
                'is_active' => true,
            ],
            [
                'slug' => 'bus',
                'name' => ['en' => 'Bus'],
                'description' => ['en' => 'Standard 40-passenger bus'],
                'is_active' => true,
            ],
        ];

        foreach ($vehicleCategories as $vehicleCategoryData) {
            VehicleCategory::updateOrCreate(
                ['slug' => $vehicleCategoryData['slug']],
                $vehicleCategoryData
            );
        }
    }
}
