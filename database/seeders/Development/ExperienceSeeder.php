<?php

namespace Database\Seeders\Development;

use App\Enums\ChargeModeEnum;
use App\Models\Base\City;
use App\Models\Base\Currency;
use App\Models\Tenant;
use App\Models\Tenants\Experience;
use App\Models\Tenants\TenantUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required data
        $tenant = Tenant::find('balopar');
        $beijing = City::where('code', 'BJS')->first();
        $shanghai = City::where('code', 'SHA')->first();
        $shenzhen = City::where('code', 'SZX')->first();
        $cny = Currency::where('code', 'CNY')->first();

        if (!$tenant || !$beijing || !$shanghai || !$shenzhen || !$cny) {
            $this->command->warn('Required data not found. Please run other seeders first.');
            return;
        }

        // Initialize tenant context
        tenancy()->initialize($tenant);

        // Get tenant user for creator_user_id
        $user = TenantUser::where('tenant_id', $tenant->id)->first();
        
        if (!$user) {
            $this->command->warn('Tenant user not found. Please run TenantSeeder first.');
            tenancy()->end();
            return;
        }

        $experiences = [
            // Beijing Experiences
            [
                'name' => [
                    'en' => 'Peking Duck Dinner',
                    'fa' => 'شام اردک پکن',
                    'zh_CN' => '北京烤鸭晚餐',
                ],
                'description' => [
                    'en' => 'Traditional Peking Duck dining experience at famous restaurant',
                    'fa' => 'تجربه غذای اردک پکن سنتی در رستوران معروف',
                    'zh_CN' => '在著名餐厅享用传统北京烤鸭',
                ],
                'slug' => 'peking-duck-dinner',
                'city_id' => $beijing->id,
                'price' => 200.00,
                'charge_mode' => ChargeModeEnum::PER_PERSON->value,
            ],
            [
                'name' => [
                    'en' => 'Kung Fu Show',
                    'fa' => 'نمایش کونگ فو',
                    'zh_CN' => '功夫表演',
                ],
                'description' => [
                    'en' => 'Live traditional Chinese martial arts performance',
                    'fa' => 'نمایش زنده هنرهای رزمی سنتی چینی',
                    'zh_CN' => '现场传统中国武术表演',
                ],
                'slug' => 'kung-fu-show',
                'city_id' => $beijing->id,
                'price' => 150.00,
                'charge_mode' => ChargeModeEnum::PER_PERSON->value,
            ],
            
            // Shanghai Experiences
            [
                'name' => [
                    'en' => 'Huangpu River Cruise',
                    'fa' => 'کروز رودخانه هوانگپو',
                    'zh_CN' => '黄浦江游船',
                ],
                'description' => [
                    'en' => 'Evening river cruise with city skyline views',
                    'fa' => 'کروز شبانه رودخانه با منظره شهر',
                    'zh_CN' => '晚间游船观赏城市天际线',
                ],
                'slug' => 'huangpu-river-cruise',
                'city_id' => $shanghai->id,
                'price' => 180.00,
                'charge_mode' => ChargeModeEnum::PER_PERSON->value,
            ],
            [
                'name' => [
                    'en' => 'Acrobatic Show',
                    'fa' => 'نمایش آکروباتیک',
                    'zh_CN' => '杂技表演',
                ],
                'description' => [
                    'en' => 'World-famous Shanghai acrobatic performance',
                    'fa' => 'نمایش آکروباتیک معروف جهانی شانگهای',
                    'zh_CN' => '世界著名的上海杂技表演',
                ],
                'slug' => 'acrobatic-show',
                'city_id' => $shanghai->id,
                'price' => 160.00,
                'charge_mode' => ChargeModeEnum::PER_PERSON->value,
            ],
            
            // Shenzhen Experiences
            [
                'name' => [
                    'en' => 'Chinese Tea Ceremony',
                    'fa' => 'مراسم چای چینی',
                    'zh_CN' => '中国茶道',
                ],
                'description' => [
                    'en' => 'Traditional Chinese tea ceremony with tea master',
                    'fa' => 'مراسم چای سنتی چینی با استاد چای',
                    'zh_CN' => '与茶道大师一起的传统中国茶道',
                ],
                'slug' => 'tea-ceremony',
                'city_id' => $shenzhen->id,
                'price' => 100.00,
                'charge_mode' => ChargeModeEnum::PER_GROUP->value,
            ],
            [
                'name' => [
                    'en' => 'Calligraphy Workshop',
                    'fa' => 'کارگاه خوشنویسی',
                    'zh_CN' => '书法工作坊',
                ],
                'description' => [
                    'en' => 'Learn Chinese calligraphy from professional artist',
                    'fa' => 'یادگیری خوشنویسی چینی از هنرمند حرفه‌ای',
                    'zh_CN' => '向专业艺术家学习中国书法',
                ],
                'slug' => 'calligraphy-workshop',
                'city_id' => $shenzhen->id,
                'price' => 500.00,
                'charge_mode' => ChargeModeEnum::PER_GROUP->value,
            ],
        ];

        foreach ($experiences as $experienceData) {
            if (!Experience::query()->where('slug', $experienceData['slug'])->exists()) {
                Experience::create([
                    'tenant_id' => $tenant->id,
                    'creator_user_id' => $user->id,
                    'is_active' => true,
                    ...$experienceData,
                ]);
            }
        }

        // End tenant context
        tenancy()->end();

        $this->command->info('✅ Experiences for tenant "balopar" created successfully!');
        $this->command->newLine();
        $this->command->line('   Beijing:');
        $this->command->line('   • Peking Duck Dinner - ¥200 (per person)');
        $this->command->line('   • Kung Fu Show - ¥150 (per person)');
        $this->command->newLine();
        $this->command->line('   Shanghai:');
        $this->command->line('   • Huangpu River Cruise - ¥180 (per person)');
        $this->command->line('   • Acrobatic Show - ¥160 (per person)');
        $this->command->newLine();
        $this->command->line('   Shenzhen:');
        $this->command->line('   • Chinese Tea Ceremony - ¥100 (per group)');
        $this->command->line('   • Calligraphy Workshop - ¥500 (per group)');
    }
}
