<?php

namespace Database\Seeders;

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
                'name' => ['en' => 'Single'],
                'capacity' => 1,
                'description' => ['en' => 'Standard single room for one person'],
                'is_active' => true,
            ],
            [
                'slug' => 'double-one',
                'name' => ['en' => 'Double for One'],
                'capacity' => 1,
                'description' => ['en' => 'Double room for single occupancy'],
                'is_active' => true,
            ],
            [
                'slug' => 'double-two',
                'name' => ['en' => 'Double for Two'],
                'capacity' => 2,
                'description' => ['en' => 'Double room with king/queen bed for two people'],
                'is_active' => true,
            ],
            [
                'slug' => 'suite-one',
                'name' => ['en' => 'Suite for One'],
                'capacity' => 1,
                'description' => ['en' => 'Suite for single occupancy'],
                'is_active' => true,
            ],
            [
                'slug' => 'suite-two',
                'name' => ['en' => 'Suite for Two'],
                'capacity' => 2,
                'description' => ['en' => 'Suite for two people'],
                'is_active' => true,
            ],
            [
                'slug' => 'twin',
                'name' => ['en' => 'Twin'],
                'capacity' => 2,
                'description' => ['en' => 'Room with two separate beds for two people'],
                'is_active' => true,
            ],
            [
                'slug' => 'triple',
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
