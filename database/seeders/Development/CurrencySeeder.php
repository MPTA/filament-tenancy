<?php

namespace Database\Seeders\Development;

use App\Models\Base\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'USD',
                'name' => [
                    'en' => 'US Dollar',
                    'fa' => 'دلار آمریکا',
                    'zh' => '美元',
                ],
                'symbol' => '$',
            ],
            [
                'code' => 'CNY',
                'name' => [
                    'en' => 'Chinese Yuan',
                    'fa' => 'یوان چین',
                    'zh' => '人民币',
                ],
                'symbol' => '¥',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
