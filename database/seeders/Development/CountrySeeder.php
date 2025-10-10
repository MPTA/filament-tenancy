<?php

namespace Database\Seeders\Development;

use App\Models\Base\Country;
use App\Models\Base\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get CNY currency
        $cny = Currency::where('code', 'CNY')->first();
        
        $countries = [
            [
                'code' => 'CN',
                'name' => [
                    'en' => 'China',
                    'fa' => 'چین',
                    'zh' => '中国',
                ],
                'currency_id' => $cny?->id,
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
