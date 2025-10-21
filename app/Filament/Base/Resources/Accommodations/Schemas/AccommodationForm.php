<?php

namespace App\Filament\Base\Resources\Accommodations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\StarRatingEnum;

class AccommodationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Accommodation Name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('star_rating')
                                    ->label('Star Rating')
                                    ->options(StarRatingEnum::getOptions())
                                    ->searchable()
                                    ->placeholder('Select star rating'),
                            ]),
                        TextInput::make('content')
                            ->label('Description')
                            ->columnSpanFull()
                            ->maxLength(500),
                        Textarea::make('address')
                            ->label('Address')
                            ->columnSpanFull()
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
                
                Section::make('Location Information')
                    ->schema([
                        Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),
                        Grid::make(2)
                            ->schema([
                                Select::make('city_id')
                                    ->label('City')
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('district_id')
                                    ->label('District')
                                    ->relationship('district', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->step(0.000001)
                                    ->rules(['numeric', 'between:-90,90'])
                                    ->validationMessages([
                                        'numeric' => 'Latitude must be a number',
                                        'between' => 'Latitude must be between -90 and 90',
                                    ]),
                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->step(0.000001)
                                    ->rules(['numeric', 'between:-180,180'])
                                    ->validationMessages([
                                        'numeric' => 'Longitude must be a number',
                                        'between' => 'Longitude must be between -180 and 180',
                                    ]),
                            ]),
                    ]),
                
                Section::make('Additional Information')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }
}
