<?php

namespace App\Filament\App\Resources\ExchangeRates\Schemas;

use App\Models\Base\Currency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExchangeRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('from_currency_id')->options(Currency::all()->pluck('name', 'id')),
                Select::make('to_currency_id')->options(Currency::all()->pluck('name', 'id')),
                TextInput::make('rate')->numeric()->required(),
            ]);
    }
}
