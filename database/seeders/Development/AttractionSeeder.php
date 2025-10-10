<?php

namespace Database\Seeders\Development;

use App\Enums\AttractionTypeEnum;
use App\Models\Base\Attraction;
use App\Models\Base\City;
use App\Models\Base\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required data
        $beijing = City::where('code', 'BJ')->first();
        $shanghai = City::where('code', 'SH')->first();
        $shenzhen = City::where('code', 'SZ')->first();
        $cny = Currency::where('code', 'CNY')->first();

        if (!$beijing || !$shanghai || !$shenzhen) {
            $this->command->warn('Required cities not found. Please run CitySeeder first.');
            return;
        }

        if (!$cny) {
            $this->command->warn('Required currency not found. Please run CurrencySeeder first.');
            return;
        }

        // Beijing Attractions
        $this->createAttraction(
            'Forbidden City',
            '故宫',
            'کاخ ممنوعه',
            'The former Chinese imperial palace from the Ming dynasty',
            'کاخ امپراتوری سابق چین از دوران سلسله مینگ',
            '明代的前中国皇宫',
            AttractionTypeEnum::MAN_MADE,
            $beijing,
            $cny,
            40,
            60,
            4.8
        );

        $this->createAttraction(
            'Great Wall of China',
            '长城',
            'دیوار بزرگ چین',
            'Ancient series of walls and fortifications',
            'مجموعه دیواره‌ها و استحکامات باستانی',
            '古代的墙壁和防御工事',
            AttractionTypeEnum::MAN_MADE,
            $beijing,
            $cny,
            45,
            70,
            4.9
        );

        $this->createAttraction(
            'Temple of Heaven',
            '天坛',
            'معبد بهشت',
            'Imperial complex of religious buildings',
            'مجموعه امپراتوری ساختمان‌های مذهبی',
            '帝国宗教建筑群',
            AttractionTypeEnum::CULTURAL,
            $beijing,
            $cny,
            30,
            45,
            4.7
        );

        // Shanghai Attractions
        $this->createAttraction(
            'The Bund',
            '外滩',
            'باند شانگهای',
            'Famous waterfront area with colonial architecture',
            'منطقه ساحلی معروف با معماری استعماری',
            '著名的殖民建筑滨水区',
            AttractionTypeEnum::MAN_MADE,
            $shanghai,
            $cny,
            0,
            0,
            4.8
        );

        $this->createAttraction(
            'Oriental Pearl Tower',
            '东方明珠塔',
            'برج مروارید شرقی',
            'Iconic TV tower with observation decks',
            'برج تلویزیونی نمادین با عرشه‌های دیدبانی',
            '标志性电视塔，设有观景台',
            AttractionTypeEnum::MAN_MADE,
            $shanghai,
            $cny,
            120,
            160,
            4.6
        );

        $this->createAttraction(
            'Yu Garden',
            '豫园',
            'باغ یو',
            'Extensive Chinese garden from the Ming dynasty',
            'باغ وسیع چینی از دوران سلسله مینگ',
            '明代的广阔中国园林',
            AttractionTypeEnum::CULTURAL,
            $shanghai,
            $cny,
            30,
            40,
            4.5
        );

        // Shenzhen Attractions
        $this->createAttraction(
            'Window of the World',
            '世界之窗',
            'پنجره دنیا',
            'Theme park with replicas of world landmarks',
            'پارک تفریحی با نمونه‌های کوچک شده از بناهای مشهور جهان',
            '主题公园，拥有世界地标的复制品',
            AttractionTypeEnum::LEISURE,
            $shenzhen,
            $cny,
            150,
            200,
            4.5
        );

        $this->createAttraction(
            'Splendid China Folk Village',
            '锦绣中华民俗村',
            'دهکده مردمی چین باشکوه',
            'Cultural theme park showcasing Chinese culture',
            'پارک تفریحی فرهنگی که فرهنگ چین را به نمایش می‌گذارد',
            '展示中国文化的文化主题公园',
            AttractionTypeEnum::CULTURAL,
            $shenzhen,
            $cny,
            140,
            180,
            4.4
        );

        $this->createAttraction(
            'Dapeng Fortress',
            '大鹏所城',
            'قلعه داپنگ',
            'Ancient military fortress from Ming dynasty',
            'قلعه نظامی باستانی از دوران سلسله مینگ',
            '明代的古代军事堡垒',
            AttractionTypeEnum::MAN_MADE,
            $shenzhen,
            $cny,
            0,
            0,
            4.3
        );

        $this->command->info('✅ Attractions seeded successfully!');
    }

    /**
     * Create attraction
     */
    private function createAttraction(
        string $nameEn,
        string $nameZh,
        string $nameFa,
        string $descEn,
        string $descFa,
        string $descZh,
        AttractionTypeEnum $type,
        City $city,
        Currency $currency,
        float $localPrice,
        float $foreignerPrice,
        float $rating
    ): void {
        Attraction::updateOrCreate(
            [
                'city_id' => $city->id,
                'name->en' => $nameEn,
            ],
            [
                'name' => [
                    'en' => $nameEn,
                    'zh' => $nameZh,
                    'fa' => $nameFa,
                ],
                'description' => [
                    'en' => $descEn,
                    'fa' => $descFa,
                    'zh' => $descZh,
                ],
                'type' => $type,
                'local_price' => $localPrice,
                'foreigner_price' => $foreignerPrice,
                'currency_id' => $currency->id,
                'country_id' => $city->province->country_id,
                'province_id' => $city->province_id,
                'city_id' => $city->id,
                'rating' => $rating,
                'is_active' => true,
            ]
        );
    }
}
