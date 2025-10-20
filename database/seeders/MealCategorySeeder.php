<?php

namespace Database\Seeders;

use App\Models\Base\MealCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MealCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'chinese',
                'name' => [
                    'en' => 'Chinese',
                    'fa' => 'چینی',
                    'zh_CN' => '中餐',
                ],
                'description' => [
                    'en' => 'Traditional Chinese cuisine',
                    'fa' => 'غذاهای سنتی چینی',
                    'zh_CN' => '传统中国菜',
                ],
            ],
            [
                'slug' => 'turkish',
                'name' => [
                    'en' => 'Turkish',
                    'fa' => 'ترکی',
                    'zh_CN' => '土耳其菜',
                ],
                'description' => [
                    'en' => 'Traditional Turkish cuisine',
                    'fa' => 'غذاهای سنتی ترکی',
                    'zh_CN' => '传统土耳其菜',
                ],
            ],
            [
                'slug' => 'persian',
                'name' => [
                    'en' => 'Persian',
                    'fa' => 'ایرانی',
                    'zh_CN' => '波斯菜',
                ],
                'description' => [
                    'en' => 'Traditional Persian cuisine',
                    'fa' => 'غذاهای سنتی ایرانی',
                    'zh_CN' => '传统波斯菜',
                ],
            ],
            [
                'slug' => 'western',
                'name' => [
                    'en' => 'Western',
                    'fa' => 'غربی',
                    'zh_CN' => '西餐',
                ],
                'description' => [
                    'en' => 'Western cuisine',
                    'fa' => 'غذاهای غربی',
                    'zh_CN' => '西方菜',
                ],
            ],
            [
                'slug' => 'fastfood',
                'name' => [
                    'en' => 'Fast Food',
                    'fa' => 'فست فود',
                    'zh_CN' => '快餐',
                ],
                'description' => [
                    'en' => 'Fast food meals',
                    'fa' => 'غذاهای آماده',
                    'zh_CN' => '快餐食品',
                ],
            ],
            [
                'slug' => 'hotpot',
                'name' => [
                    'en' => 'Hotpot',
                    'fa' => 'هات‌پات',
                    'zh_CN' => '火锅',
                ],
                'description' => [
                    'en' => 'Traditional hotpot dining',
                    'fa' => 'غذای هات‌پات سنتی',
                    'zh_CN' => '传统火锅',
                ],
            ],
            [
                'slug' => 'japanese',
                'name' => [
                    'en' => 'Japanese',
                    'fa' => 'ژاپنی',
                    'zh_CN' => '日本料理',
                ],
                'description' => [
                    'en' => 'Traditional Japanese cuisine',
                    'fa' => 'غذاهای سنتی ژاپنی',
                    'zh_CN' => '传统日本料理',
                ],
            ],
            [
                'slug' => 'arabian',
                'name' => [
                    'en' => 'Arabian',
                    'fa' => 'عربی',
                    'zh_CN' => '阿拉伯菜',
                ],
                'description' => [
                    'en' => 'Traditional Arabian cuisine',
                    'fa' => 'غذاهای سنتی عربی',
                    'zh_CN' => '传统阿拉伯菜',
                ],
            ],
            [
                'slug' => 'seafood',
                'name' => [
                    'en' => 'Seafood',
                    'fa' => 'دریایی',
                    'zh_CN' => '海鲜',
                ],
                'description' => [
                    'en' => 'Fresh seafood dishes',
                    'fa' => 'غذاهای دریایی تازه',
                    'zh_CN' => '新鲜海鲜',
                ],
            ],
            [
                'slug' => 'buffet-breakfast',
                'name' => [
                    'en' => 'Buffet Breakfast',
                    'fa' => 'بوفه صبحانه',
                    'zh_CN' => '自助早餐',
                ],
                'description' => [
                    'en' => 'All-you-can-eat breakfast buffet',
                    'fa' => 'بوفه صبحانه آزاد',
                    'zh_CN' => '自助早餐',
                ],
            ],
        ];

        foreach ($categories as $category) {
            MealCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('✅ Meal categories seeded successfully!');
    }
}
