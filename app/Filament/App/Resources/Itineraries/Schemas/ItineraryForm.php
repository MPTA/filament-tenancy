<?php

namespace App\Filament\App\Resources\Itineraries\Schemas;

use App\Enums\StarRatingEnum;
use App\Enums\TravelModeEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItineraryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('days')->columnSpanFull()
                    ->schema([
                        Select::make('current_city_id')
                            ->relationship('currentCity', 'name')
                            ->required(),
                        Select::make('accommodation_city_id')
                        ->relationship('accommodationCity', 'name'),
                        Select::make('accommodation_star_rating')->options(StarRatingEnum::getOptions()),
                        Toggle::make('has_vehicle')->label('Has Car'),
                        Toggle::make('has_tour_guide'),
                    ])
                    ->relationship('days')
                    ->required(),
            ]);
    }
}
