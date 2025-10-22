<?php

namespace App\Filament\Base\Resources\Cities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('City Information')
                    ->schema([
                        Select::make('province_id')
                            ->label('Province')
                            ->relationship('province', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->hint('Select the province this city belongs to'),
                        TextInput::make('name')
                            ->label('City Name')
                            ->required()
                            ->maxLength(255)
                            ->hint('Enter city name in different languages'),
                        TextInput::make('code')
                            ->label('City Code')
                            ->maxLength(10)
                            ->hint('Optional city code'),
                        Toggle::make('has_code')
                            ->label('Has Code')
                            ->default(false)
                            ->hint('Indicate if this city has an official code'),
                        TextInput::make('native')
                            ->label('Native Name')
                            ->maxLength(255)
                            ->hint('City name in native language'),
                    ])
                    ->columns(2),

                Section::make('Geographic Information')
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->step(0.00000001)
                            ->placeholder('e.g., 35.6892')
                            ->hint('Latitude coordinate'),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->step(0.00000001)
                            ->placeholder('e.g., 51.3890')
                            ->hint('Longitude coordinate'),
                        TextInput::make('timezone')
                            ->label('Timezone')
                            ->maxLength(50)
                            ->placeholder('e.g., Asia/Tehran')
                            ->hint('IANA timezone identifier'),
                        TextInput::make('wiki_data_id')
                            ->label('WikiData ID')
                            ->maxLength(50)
                            ->placeholder('e.g., Q3616')
                            ->hint('WikiData identifier for this city'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
