<?php

namespace App\Filament\Base\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Country Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Country Name')
                            ->required()
                            ->maxLength(255)
                            ->hint('Enter country name in different languages'),
                        TextInput::make('code')
                            ->label('Country Code')
                            ->required()
                            ->maxLength(3)
                            ->minLength(2)
                            ->unique(ignoreRecord: true)
                            ->hint('ISO 3166-1 alpha-2 or alpha-3 country code (e.g., US, USA)')
                            ->placeholder('e.g., US, USA, IR, IRL'),
                    ])
                    ->columns(1),
            ]);
    }
}
