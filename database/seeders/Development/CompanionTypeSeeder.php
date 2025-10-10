<?php

namespace Database\Seeders\Development;

use App\Models\Base\CompanionCategory;
use App\Models\Base\Language;
use App\Models\Tenant;
use App\Models\Tenants\CompanionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required data
        $tenant = Tenant::find('balopar');
        $english = Language::where('code', 'en')->first();
        $chinese = Language::where('code', 'zh')->first();
        $tourGuide = CompanionCategory::where('slug', 'tour-guide')->first();
        $translator = CompanionCategory::where('slug', 'translator')->first();

        if (!$tenant || !$english || !$chinese || !$tourGuide || !$translator) {
            $this->command->warn('Required data not found. Please run other seeders first.');
            return;
        }

        // Initialize tenant context
        tenancy()->initialize($tenant);

        // 1. English Guide (چینی که انگلیسی صحبت می‌کند)
        if (!CompanionType::query()->where('slug', 'english-guide')->exists()) {
            CompanionType::create([
                'name' => [
                    'en' => 'English Speaking Guide',
                    'fa' => 'راهنمای انگلیسی زبان',
                    'zh' => '英语导游',
                ],
                'slug' => 'english-guide',
                'tenant_id' => $tenant->id,
                'native_language_id' => $chinese->id, // چینی
                'speaking_language_id' => $english->id, // انگلیسی صحبت می‌کنه
                'companion_category_id' => $tourGuide->id,
                'per_day_price' => 300.00,
                'half_day_price' => 180.00,
                'per_hour_price' => 40.00,
                'max_hour_per_day' => 8,
                'max_hour_half_day' => 4,
                'extra_hour_price' => 50.00,
            ]);
        }

        // 2. English Translator (انگلیسی که چینی صحبت می‌کند)
        if (!CompanionType::query()->where('slug', 'english-translator')->exists()) {
            CompanionType::create([
                'name' => [
                    'en' => 'English Translator',
                    'fa' => 'مترجم انگلیسی',
                    'zh' => '英语翻译',
                ],
                'slug' => 'english-translator',
                'tenant_id' => $tenant->id,
                'native_language_id' => $english->id, // انگلیسی
                'speaking_language_id' => $chinese->id, // چینی صحبت می‌کنه
                'companion_category_id' => $translator->id,
                'per_day_price' => 350.00,
                'half_day_price' => 200.00,
                'per_hour_price' => 45.00,
                'max_hour_per_day' => 8,
                'max_hour_half_day' => 4,
                'extra_hour_price' => 55.00,
            ]);
        }

        // End tenant context
        tenancy()->end();

        $this->command->info('✅ Companion types for tenant "balopar" created successfully!');
        $this->command->line('   1. English Speaking Guide (Chinese native, speaks English)');
        $this->command->line('      - Per Day: ¥300 | Half Day: ¥180 | Per Hour: ¥40');
        $this->command->line('   2. English Translator (English native, speaks Chinese)');
        $this->command->line('      - Per Day: ¥350 | Half Day: ¥200 | Per Hour: ¥45');
    }
}
