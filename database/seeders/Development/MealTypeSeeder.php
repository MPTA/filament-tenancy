<?php

namespace Database\Seeders\Development;

use App\Models\Base\MealCategory;
use App\Models\Tenant;
use App\Models\Tenants\MealType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MealTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required data
        $tenant = Tenant::find('balopar');

        if (!$tenant) {
            $this->command->warn('Tenant "balopar" not found. Please run TenantSeeder first.');
            return;
        }

        // Get meal categories
        $chinese = MealCategory::where('slug', 'chinese')->first();
        $hotpot = MealCategory::where('slug', 'hotpot')->first();
        $buffetBreakfast = MealCategory::where('slug', 'buffet-breakfast')->first();
        $turkish = MealCategory::where('slug', 'turkish')->first();

        if (!$chinese || !$hotpot || !$buffetBreakfast || !$turkish) {
            $this->command->warn('Required meal categories not found. Please run MealCategorySeeder first.');
            return;
        }

        // Initialize tenant context
        tenancy()->initialize($tenant);

        $mealTypes = [
            [
                'name' => [
                    'en' => 'Chinese Standard',
                    'fa' => 'چینی استاندارد',
                    'zh_CN' => '标准中餐',
                ],
                'slug' => 'chinese-standard',
                'meal_category_id' => $chinese->id,
                'price' => 80.00,
            ],
            [
                'name' => [
                    'en' => 'Chinese Hotpot',
                    'fa' => 'هات‌پات چینی',
                    'zh_CN' => '中式火锅',
                ],
                'slug' => 'chinese-hotpot',
                'meal_category_id' => $hotpot->id,
                'price' => 150.00,
            ],
            [
                'name' => [
                    'en' => 'Buffet Breakfast',
                    'fa' => 'بوفه صبحانه',
                    'zh_CN' => '自助早餐',
                ],
                'slug' => 'buffet-breakfast',
                'meal_category_id' => $buffetBreakfast->id,
                'price' => 50.00,
            ],
            [
                'name' => [
                    'en' => 'Turkish Standard',
                    'fa' => 'ترکی استاندارد',
                    'zh_CN' => '标准土耳其菜',
                ],
                'slug' => 'turkish-standard',
                'meal_category_id' => $turkish->id,
                'price' => 120.00,
            ],
        ];

        foreach ($mealTypes as $mealTypeData) {
            if (!MealType::query()->where('slug', $mealTypeData['slug'])->exists()) {
                MealType::create([
                    'tenant_id' => $tenant->id,
                    ...$mealTypeData,
                ]);
            }
        }

        // End tenant context
        tenancy()->end();

        $this->command->info('✅ Meal types for tenant "balopar" created successfully!');
        $this->command->line('   1. Chinese Standard - ¥80');
        $this->command->line('   2. Chinese Hotpot - ¥150');
        $this->command->line('   3. Buffet Breakfast - ¥50');
        $this->command->line('   4. Turkish Standard - ¥120');
    }
}
