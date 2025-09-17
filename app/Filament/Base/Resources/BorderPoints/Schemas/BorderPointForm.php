<?php

namespace App\Filament\Base\Resources\BorderPoints\Schemas;

use App\Models\Base\City;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BorderPointForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Border Point Information')
                    ->description('Enter the border point details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Border Point Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Torkham Border, Wagah Border')
                            ->helperText('Full name of the border point'),
                        
                        TextInput::make('iata_code')
                            ->label('IATA Code')
                            ->maxLength(3)
                            ->minLength(3)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., TKM, WAG')
                            ->helperText('3-letter IATA code (optional)')
                            ->rules(['regex:/^[A-Z]{3}$/'])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('iata_code', strtoupper($state));
                                }
                            }),
                        
                        Select::make('city_id')
                            ->label('City')
                            ->relationship('city', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a city')
                            ->helperText('City where the border point is located')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('province_id')
                                    ->relationship('province', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columns(2)
            ]);
    }
}
