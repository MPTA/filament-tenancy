<?php

namespace Database\Seeders;

use App\Enums\RoomCategoryEnum;
use App\Models\Base\RoomCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            [
                'slug' => 'single',
                'category' => RoomCategoryEnum::SINGLE,
                'name' => [
                    'en' => 'Single',
                    'zh_CN' => '单人间',
                ],
                'capacity' => 1,
                'description' => [
                    'en' => 'Standard single room for one person',
                    'zh_CN' => '标准单人间',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'double-one',
                'category' => RoomCategoryEnum::DOUBLE_FOR_ONE,
                'name' => [
                    'en' => 'Double for One',
                    'zh_CN' => '单人使用双人间',
                ],
                'capacity' => 1,
                'description' => [
                    'en' => 'Double room for single occupancy',
                    'zh_CN' => '双人间单人入住',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'double-two',
                'category' => RoomCategoryEnum::DOUBLE_FOR_TWO,
                'name' => [
                    'en' => 'Double for Two',
                    'zh_CN' => '双人间',
                ],
                'capacity' => 2,
                'description' => [
                    'en' => 'Double room with king/queen bed for two people',
                    'zh_CN' => '双人大床房，适合两人入住',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'suite-one',
                'category' => RoomCategoryEnum::SUITE_FOR_ONE,
                'name' => [
                    'en' => 'Suite for One',
                    'zh_CN' => '单人套房',
                ],
                'capacity' => 1,
                'description' => [
                    'en' => 'Suite for single occupancy',
                    'zh_CN' => '单人入住套房',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'suite-two',
                'category' => RoomCategoryEnum::SUITE_FOR_TWO,
                'name' => [
                    'en' => 'Suite for Two',
                    'zh_CN' => '双人套房',
                ],
                'capacity' => 2,
                'description' => [
                    'en' => 'Suite for two people',
                    'zh_CN' => '两人入住套房',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'twin',
                'category' => RoomCategoryEnum::TWIN,
                'name' => [
                    'en' => 'Twin',
                    'zh_CN' => '双床间',
                ],
                'capacity' => 2,
                'description' => [
                    'en' => 'Room with two separate beds for two people',
                    'zh_CN' => '标准双床房，两张单人床',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'triple',
                'category' => RoomCategoryEnum::TRIPLE,
                'name' => [
                    'en' => 'Triple',
                    'zh_CN' => '三人间',
                ],
                'capacity' => 3,
                'description' => [
                    'en' => 'Room for three people',
                    'zh_CN' => '三人房，适合三人入住',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($roomTypes as $roomTypeData) {
            RoomCategory::updateOrCreate(
                ['slug' => $roomTypeData['slug']],
                $roomTypeData
            );
        }
    }
}
