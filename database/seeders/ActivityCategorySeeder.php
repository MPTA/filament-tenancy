<?php

namespace Database\Seeders;

use App\Enums\ActivityCategoryTypeEnum;
use App\Models\Base\ActivityCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'attraction',
                'type' => ActivityCategoryTypeEnum::ATTRACTION->value,
                'name' => [
                    'en' => 'Attraction',
                    'fa' => 'جاذبه گردشگری',
                    'zh_CN' => '景点',
                ],
            ],
            [
                'slug' => 'experience',
                'type' => ActivityCategoryTypeEnum::EXPERIENCE->value,
                'name' => [
                    'en' => 'Experience',
                    'fa' => 'تجربه',
                    'zh_CN' => '体验',
                ],
            ],
            [
                'slug' => 'meal',
                'type' => ActivityCategoryTypeEnum::MEAL->value,
                'name' => [
                    'en' => 'Meal',
                    'fa' => 'وعده غذایی',
                    'zh_CN' => '餐饮',
                ],
            ],
            [
                'slug' => 'ticket',
                'type' => ActivityCategoryTypeEnum::TICKET->value,
                'name' => [
                    'en' => 'Ticket',
                    'fa' => 'بلیط',
                    'zh_CN' => '门票',
                ],
            ],
        ];

        foreach ($categories as $category) {
            ActivityCategory::updateOrCreate(
                ['type' => $category['type']],
                $category
            );
        }

        $this->command->info('✅ Activity categories seeded successfully!');
    }
}
