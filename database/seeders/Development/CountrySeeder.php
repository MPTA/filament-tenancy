<?php

namespace Database\Seeders\Development;

use App\Models\Base\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'code' => 'CN',
                'name' => [
                    'en' => 'China',
                    'fa' => 'چین',
                    'zh' => '中国',
                ],
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
