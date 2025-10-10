<?php

namespace Database\Seeders\Development;

use App\Models\Base\City;
use App\Models\Base\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get provinces
        $beijing = Province::where('code', 'BJ')->first();
        $shanghai = Province::where('code', 'SH')->first();
        $guangdong = Province::where('code', 'GD')->first();

        if (!$beijing || !$shanghai || !$guangdong) {
            $this->command->warn('Required provinces not found. Please run ProvinceSeeder first.');
            return;
        }

        $cities = [
            [
                'province_id' => $beijing->id,
                'code' => 'BJ',
                'name' => [
                    'en' => 'Beijing',
                    'fa' => 'پکن',
                    'zh' => '北京',
                ],
            ],
            [
                'province_id' => $shanghai->id,
                'code' => 'SH',
                'name' => [
                    'en' => 'Shanghai',
                    'fa' => 'شانگهای',
                    'zh' => '上海',
                ],
            ],
            [
                'province_id' => $guangdong->id,
                'code' => 'SZ',
                'name' => [
                    'en' => 'Shenzhen',
                    'fa' => 'شنژن',
                    'zh' => '深圳',
                ],
            ],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                [
                    'province_id' => $city['province_id'],
                    'code' => $city['code']
                ],
                $city
            );
        }
    }
}
