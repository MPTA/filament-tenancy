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
                'name' => [
                    'en' => 'Sedan',
                    'zh_CN' => '轿车',
                ],
                'description' => [
                    'en' => 'Standard 4-passenger sedan',
                    'zh_CN' => '标准4座轿车',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'suv',
                'name' => [
                    'en' => 'SUV',
                    'zh_CN' => 'SUV越野车',
                ],
                'description' => [
                    'en' => '5-passenger SUV',
                    'zh_CN' => '5座SUV越野车',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'minivan',
                'name' => [
                    'en' => 'Minivan',
                    'zh_CN' => '商务车',
                ],
                'description' => [
                    'en' => '7-passenger minivan',
                    'zh_CN' => '7座商务车',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'van',
                'name' => [
                    'en' => 'Van',
                    'zh_CN' => '面包车',
                ],
                'description' => [
                    'en' => '12-passenger van',
                    'zh_CN' => '12座面包车',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'minibus',
                'name' => [
                    'en' => 'Minibus',
                    'zh_CN' => '小巴',
                ],
                'description' => [
                    'en' => 'Standard 20-passenger minibus',
                    'zh_CN' => '标准20座小巴',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'bus',
                'name' => [
                    'en' => 'Bus',
                    'zh_CN' => '大巴',
                ],
                'description' => [
                    'en' => 'Standard 40-passenger bus',
                    'zh_CN' => '标准40座大巴',
                ],
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
