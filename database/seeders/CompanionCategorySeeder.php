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
                'category_type' => 'tour_guide',
                'name' => [
                    'en' => 'Tour Guide',
                    'zh_CN' => '导游',
                ],
                'description' => [
                    'en' => 'Local guide in destination',
                    'zh_CN' => '目的地当地导游',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'tour-leader',
                'category_type' => 'driver',
                'name' => [
                    'en' => 'Tour Leader',
                    'zh_CN' => '领队',
                ],
                'description' => [
                    'en' => 'Escort from origin',
                    'zh_CN' => '出发地陪同领队',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'translator',
                'category_type' => 'translator',
                'name' => [
                    'en' => 'Translator',
                    'zh_CN' => '翻译',
                ],
                'description' => [
                    'en' => 'Interpreter for the group',
                    'zh_CN' => '团队翻译',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'staff',
                'category_type' => 'staff',
                'name' => [
                    'en' => 'Staff',
                    'zh_CN' => '工作人员',
                ],
                'description' => [
                    'en' => 'General companion or internal manager',
                    'zh_CN' => '一般陪同人员或内部管理员',
                ],
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
