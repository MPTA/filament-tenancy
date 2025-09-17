<?php

namespace App\Filament\Tenant\Resources\ExchangeRates\Schemas;

use App\Models\Base\Currency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExchangeRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Exchange Rate Information')
                    ->description('Enter the exchange rate details')
                    ->schema([
                        Select::make('from_currency_id')
                            ->label('From Currency')
                            ->options(Currency::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select source currency'),
                        
                        Select::make('to_currency_id')
                            ->label('To Currency')
                            ->options(Currency::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select target currency')
                            ->rules(['different:from_currency_id']),
                        
                        TextInput::make('rate')
                            ->label('Exchange Rate')
                            ->numeric()
                            ->required()
                            ->step(0.000001)
                            ->placeholder('e.g., 1.25')
                            ->helperText('Enter the exchange rate (1 from currency = X to currency)')
                            ->rules(['min:0.000001']),
                    ])
                    ->columns(2)
            ]);
    }
}
