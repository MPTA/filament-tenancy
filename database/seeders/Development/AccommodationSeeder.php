<?php

namespace Database\Seeders\Development;

use App\Models\Base\Accommodation;
use App\Models\Base\AccommodationPrice;
use App\Models\Base\City;
use App\Models\Base\Currency;
use App\Models\Base\RoomCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required data
        $beijing = City::where('code', 'BJS')->first();
        $shanghai = City::where('code', 'SHA')->first();
        $shenzhen = City::where('code', 'SZX')->first();
        $usd = Currency::where('code', 'USD')->first();
        $cny = Currency::where('code', 'CNY')->first();
        $single = RoomCategory::where('slug', 'single')->first();
        $twin = RoomCategory::where('slug', 'twin')->first();

        if (!$beijing || !$shanghai || !$shenzhen) {
            $this->command->warn('Required cities not found. Please run CitySeeder first.');
            return;
        }

        if (!$usd || !$cny) {
            $this->command->warn('Required currencies not found. Please run CurrencySeeder first.');
            return;
        }

        if (!$single || !$twin) {
            $this->command->warn('Required room categories not found. Please run RoomCategorySeeder first.');
            return;
        }

        // Beijing Hotels
        $this->createHotel(
            'Beijing Grand Hotel',
            '北京大饭店',
            'هتل بزرگ پکن',
            4,
            $beijing,
            $cny,
            [
                ['room' => $single, 'price' => 800],
                ['room' => $twin, 'price' => 650],
            ]
        );

        $this->createHotel(
            'Beijing Luxury Palace',
            '北京豪华宫殿',
            'کاخ لوکس پکن',
            5,
            $beijing,
            $cny,
            [
                ['room' => $single, 'price' => 1200,'is_include_breakfast'=>true],
                ['room' => $twin, 'price' => 950,'is_include_breakfast'=>true],
            ]
        );

        // Shanghai Hotels
        $this->createHotel(
            'Shanghai Imperial Hotel',
            '上海帝国酒店',
            'هتل امپراطوری شانگهای',
            4,
            $shanghai,
            $cny,
            [
                ['room' => $single, 'price' => 850],
                ['room' => $twin, 'price' => 700],
            ]
        );

        $this->createHotel(
            'Shanghai Royal Suites',
            '上海皇家套房',
            'سوئیت‌های سلطنتی شانگهای',
            5,
            $shanghai,
            $cny,
            [
                ['room' => $single, 'price' => 1300,'is_include_breakfast'=>true],
                ['room' => $twin, 'price' => 1000,'is_include_breakfast'=>true],
            ]
        );

        // Shenzhen Hotels
        $this->createHotel(
            'Shenzhen Bay Hotel',
            '深圳湾酒店',
            'هتل خلیج شنژن',
            4,
            $shenzhen,
            $cny,
            [
                ['room' => $single, 'price' => 750],
                ['room' => $twin, 'price' => 600],
            ]
        );

        $this->createHotel(
            'Shenzhen Pearl Tower',
            '深圳珍珠塔',
            'برج مروارید شنژن',
            5,
            $shenzhen,
            $cny,
            [
                ['room' => $single, 'price' => 1100,'is_include_breakfast'=>true],
                ['room' => $twin, 'price' => 900,'is_include_breakfast'=>true],
            ]
        );

        $this->command->info('✅ Accommodations seeded successfully!');
    }

    /**
     * Create hotel with prices
     */
    private function createHotel(
        string $nameEn,
        string $nameZh,
        string $nameFa,
        int $stars,
        City $city,
        Currency $currency,
        array $roomPrices
    ): void {
        $accommodation = Accommodation::updateOrCreate(
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
                'content' => [
                    'en' => "Luxury accommodation in {$city->getTranslation('name', 'en')}",
                    'zh' => "{$city->getTranslation('name', 'zh')}的豪华住宿",
                    'fa' => "اقامتگاه لوکس در {$city->getTranslation('name', 'en')}",
                ],
                'star_rating' => $stars,
                'country_id' => $city->province->country_id,
                'province_id' => $city->province_id,
                'city_id' => $city->id,
                'is_active' => true,
            ]
        );

        // Create prices for each room type
        foreach ($roomPrices as $roomPrice) {
            AccommodationPrice::updateOrCreate(
                [
                    'accommodation_id' => $accommodation->id,
                    'room_category_id' => $roomPrice['room']->id,
                ],
                [
                    'price' => $roomPrice['price'],
                    'is_include_breakfast' => $roomPrice['is_include_breakfast'] ?? false,
                    'valid_from' => now()->startOfYear(),
                    'valid_to' => now()->endOfYear(),
                ]
            );
        }
    }
}
