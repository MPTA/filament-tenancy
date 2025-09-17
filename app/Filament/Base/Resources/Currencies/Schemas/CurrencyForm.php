<?php

namespace App\Filament\Base\Resources\Currencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Currency Information')
                    ->description('Enter the currency details')
                    ->schema([
                        TextInput::make('code')
                            ->label('Currency Code')
                            ->required()
                            ->maxLength(3)
                            ->minLength(3)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., USD, EUR, GBP')
                            ->helperText('3-letter ISO currency code')
                            ->rules(['regex:/^[A-Z]{3}$/'])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                $set('code', strtoupper($state));
                            }),
                        
                        TextInput::make('name')
                            ->label('Currency Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., US Dollar, Euro, British Pound')
                            ->helperText('Full name of the currency'),
                        
                        TextInput::make('symbol')
                            ->label('Currency Symbol')
                            ->maxLength(10)
                            ->placeholder('e.g., $, €, £')
                            ->helperText('Symbol used to represent the currency'),
                    ])
                    ->columns(2)
            ]);
    }
}
