<?php

namespace App\Filament\Base\Resources\Airports\Schemas;

use App\Models\Base\City;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AirportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Airport Information')
                    ->description('Enter the airport details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Airport Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., John F. Kennedy International Airport')
                            ->helperText('Full name of the airport'),
                        
                        TextInput::make('iata_code')
                            ->label('IATA Code')
                            ->required()
                            ->maxLength(3)
                            ->minLength(3)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., JFK, LAX, LHR')
                            ->helperText('3-letter IATA airport code')
                            ->rules(['regex:/^[A-Z]{3}$/'])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                $set('iata_code', strtoupper($state));
                            }),
                        
                        Select::make('city_id')
                            ->label('City')
                            ->relationship('city', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a city')
                            ->helperText('City where the airport is located')
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

