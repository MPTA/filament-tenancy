<?php

namespace App\Filament\App\Resources\Itineraries\Schemas;

use App\Enums\StarRatingEnum;
use App\Enums\TravelModeEnum;
use App\Models\Base\Accommodation;
use App\Models\Base\Attraction;
use App\Models\Base\City;
use App\Models\Base\SubAttraction;
use App\Models\Tenants\MealType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
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
                Repeater::make('days')->columnSpanFull()->columns(['md' => 2, 'lg' => 4])
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
                            ->label('Star Rating')
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
                        Select::make('breakfast')->options(MealType::getCachedSelectOptions())->columnStart(1),
                        Select::make('lunch')->options(MealType::getCachedSelectOptions()),
                        Select::make('dinner')->options(MealType::getCachedSelectOptions()),
                        Repeater::make('attractions')
                            ->columnStart(1)
                            ->columnSpanFull()
                            ->label('Attractions')
                            ->table([
                                TableColumn::make('City'),
                                TableColumn::make('Attraction'),
                                TableColumn::make('Outview'),
                                TableColumn::make('Sub Attractions'),
                            ])
                            ->schema([
                                Select::make('city_id')
                                    ->label('City')
                                    ->options(City::getCachedSelectOptions())
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('attraction_id', null);
                                        $set('sub_attractions', []);
                                    }),
                                
                                Select::make('attraction_id')
                                    ->label('Main Attraction')
                                    ->options(function (callable $get) {
                                        $cityId = $get('city_id');
                                        if (!$cityId) {
                                            return [];
                                        }
                                        
                                        return Attraction::where('city_id', $cityId)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('sub_attractions', []);
                                    }),
                                Toggle::make('is_outview')
                                    ->label('Outview')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // اگر outview فعال شد، sub_attractions رو پاک کن
                                        if ($state) {
                                            $set('sub_attractions', []);
                                        }
                                    }),
                                
                                Select::make('sub_attractions')
                                    ->label('Sub Attractions')
                                    ->multiple()
                                    ->options(function (callable $get) {
                                        $attractionId = $get('attraction_id');
                                        if (!$attractionId) {
                                            return [];
                                        }
                                        
                                        return SubAttraction::where('attraction_id', $attractionId)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->visible(fn (callable $get) => !empty($get('attraction_id')) && !$get('is_outview'))
                                    ->disabled(fn (callable $get) => $get('is_outview'))
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->addActionLabel('Add Attraction')
                            ->reorderable()
                            ->collapsible(),
                    ])
                    ->relationship('days')
                    ->required(),
            ]);
    }
}
