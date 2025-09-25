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
                'name' => ['en' => 'Single'],
                'capacity' => 1,
                'description' => ['en' => 'Standard single room for one person'],
                'is_active' => true,
            ],
            [
                'slug' => 'double-one',
                'category' => RoomCategoryEnum::DOUBLE_FOR_ONE,
                'name' => ['en' => 'Double for One'],
                'capacity' => 1,
                'description' => ['en' => 'Double room for single occupancy'],
                'is_active' => true,
            ],
            [
                'slug' => 'double-two',
                'category' => RoomCategoryEnum::DOUBLE_FOR_TWO,
                'name' => ['en' => 'Double for Two'],
                'capacity' => 2,
                'description' => ['en' => 'Double room with king/queen bed for two people'],
                'is_active' => true,
            ],
            [
                'slug' => 'suite-one',
                'category' => RoomCategoryEnum::SUITE_FOR_ONE,
                'name' => ['en' => 'Suite for One'],
                'capacity' => 1,
                'description' => ['en' => 'Suite for single occupancy'],
                'is_active' => true,
            ],
            [
                'slug' => 'suite-two',
                'category' => RoomCategoryEnum::SUITE_FOR_TWO,
                'name' => ['en' => 'Suite for Two'],
                'capacity' => 2,
                'description' => ['en' => 'Suite for two people'],
                'is_active' => true,
            ],
            [
                'slug' => 'twin',
                'category' => RoomCategoryEnum::TWIN,
                'name' => ['en' => 'Twin'],
                'capacity' => 2,
                'description' => ['en' => 'Room with two separate beds for two people'],
                'is_active' => true,
            ],
            [
                'slug' => 'triple',
                'category' => RoomCategoryEnum::TRIPLE,
                'name' => ['en' => 'Triple'],
                'capacity' => 3,
                'description' => ['en' => 'Room for three people'],
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
