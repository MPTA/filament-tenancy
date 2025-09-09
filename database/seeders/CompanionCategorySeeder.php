<?php

namespace Database\Seeders;

use App\Models\Base\CompanionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companionCategories = [
            [
                'slug' => 'tour-guide',
                'name' => ['en' => 'Tour Guide'],
                'description' => ['en' => 'Local guide in destination'],
                'is_active' => true,
            ],
            [
                'slug' => 'tour-leader',
                'name' => ['en' => 'Tour Leader'],
                'description' => ['en' => 'Escort from origin'],
                'is_active' => true,
            ],
            [
                'slug' => 'translator',
                'name' => ['en' => 'Translator'],
                'description' => ['en' => 'Interpreter for the group'],
                'is_active' => true,
            ],
            [
                'slug' => 'staff',
                'name' => ['en' => 'Staff'],
                'description' => ['en' => 'General companion or internal manager'],
                'is_active' => true,
            ],
        ];

        foreach ($companionCategories as $companionCategoryData) {
            CompanionCategory::updateOrCreate(
                ['slug' => $companionCategoryData['slug']],
                $companionCategoryData
            );
        }
    }
}
