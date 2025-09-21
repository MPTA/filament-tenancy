<?php

namespace App\Filament\App\Resources\Itineraries\Schemas;

use App\Enums\StarRatingEnum;
use App\Enums\TravelModeEnum;
use App\Models\Base\Accommodation;
use App\Models\Tenants\MealType;
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
                            ->relationship('accommodationCity', 'name')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('accommodation_id', null);
                            }),
                        Select::make('accommodation_star_rating')
                            ->options(StarRatingEnum::getOptions())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('accommodation_id', null);
                            }),
                        Select::make('accommodation_id')
                            ->options(function (callable $get) {
                                $accommodationCityId = $get('accommodation_city_id');
                                $starRating = $get('accommodation_star_rating');
                                
                                if (!$accommodationCityId) {
                                    return [];
                                }
                                
                                $query = Accommodation::query()
                                    ->where('city_id', $accommodationCityId);
                                
                                if ($starRating) {
                                    $query->where('star_rating', $starRating);
                                }
                                
                                return $query->pluck('name', 'id')->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // اگر accommodation انتخاب شد و star rating خالی بود
                                if ($state && !$get('accommodation_star_rating')) {
                                    $accommodation = Accommodation::find($state);
                                    if ($accommodation?->star_rating) {
                                        $set('accommodation_star_rating', $accommodation->star_rating);
                                    }
                                }
                            }),
                        Toggle::make('has_vehicle')->label('Has Car'),
                        Toggle::make('has_tour_guide'),
                        Select::make('breakfast')->options(MealType::getCachedSelectOptions()),
                        Select::make('lunch')->options(MealType::getCachedSelectOptions()),
                        Select::make('dinner')->options(MealType::getCachedSelectOptions())
                    ])
                    ->relationship('days')
                    ->required(),
            ]);
    }
}
