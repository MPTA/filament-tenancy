<?php

namespace Database\Seeders\Development;

use App\Models\Base\Country;
use App\Models\Base\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $china = Country::where('code', 'CN')->first();
        
        if (!$china) {
            $this->command->warn('China country not found. Please run CountrySeeder first.');
            return;
        }

        $provinces = [
            [
                'country_id' => $china->id,
                'code' => 'BJ',
                'name' => [
                    'en' => 'Beijing Municipality',
                    'fa' => 'شهرداری پکن',
                    'zh' => '北京市',
                ],
            ],
            [
                'country_id' => $china->id,
                'code' => 'SH',
                'name' => [
                    'en' => 'Shanghai Municipality',
                    'fa' => 'شهرداری شانگهای',
                    'zh' => '上海市',
                ],
            ],
            [
                'country_id' => $china->id,
                'code' => 'GD',
                'name' => [
                    'en' => 'Guangdong Province',
                    'fa' => 'استان گوانگدونگ',
                    'zh' => '广东省',
                ],
            ],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(
                [
                    'country_id' => $province['country_id'],
                    'code' => $province['code']
                ],
                $province
            );
        }
    }
}
