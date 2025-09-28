<?php

namespace App\Filament\App\Resources\Itineraries\Schemas;

use App\Enums\StarRatingEnum;
use App\Enums\TicketClassEnum;
use App\Enums\TransportModeEnum;
use App\Enums\TravelModeEnum;
use App\Enums\VehicleUsageModeEnum;
use App\Models\Base\Accommodation;
use App\Models\Base\Attraction;
use App\Models\Base\City;
use App\Models\Base\SubAttraction;
use App\Models\Tenants\Experience;
use App\Models\Tenants\MealType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ItineraryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('days')
                    ->columnSpanFull()
                    ->columns(['md' => 2, 'lg' => 4])
                    ->label('Days')
                    ->live()
                    ->itemLabel(function (array $state, $component) {
                        $dayNumber = $component->getStatePath() ? (int) substr($component->getStatePath(), -1) + 1 : 1;
                        
                        $cityName = '';
                        $hotelName = '';
                        
                        // Get city name - prefer accommodation city, fallback to current city
                        if (!empty($state['accommodation_city_id'])) {
                            $city = City::find($state['accommodation_city_id']);
                            if ($city) {
                                $cityName = $city->name;
                            }
                        }
                        
                        // If no accommodation city, use current city
                        if (empty($cityName) && !empty($state['current_city_id'])) {
                            $city = City::find($state['current_city_id']);
                            if ($city) {
                                $cityName = $city->name;
                            }
                        }
                        
                        // Get hotel name and star rating
                        if (!empty($state['accommodation_id'])) {
                            $accommodation = Accommodation::find($state['accommodation_id']);
                            if ($accommodation) {
                                $hotelName = $accommodation->name;
                                
                                // Add star rating
                                if (!empty($state['accommodation_star_rating'])) {
                                    $starRating = (int) $state['accommodation_star_rating'];
                                    $stars = str_repeat('⭐', $starRating);
                                    $hotelName .= " {$stars}";
                                }
                            }
                        }
                        
                        $label = "Day {$dayNumber}";
                        
                        if ($cityName) {
                            $label .= " - {$cityName}";
                        }
                        
                        if ($hotelName) {
                            $label .= " ({$hotelName})";
                        }
                        
                        // Add vehicle icon
                        if (!empty($state['has_vehicle']) && $state['has_vehicle']) {
                            $label .= " 🚗";
                        }
                        
                        // Add tour guide icon
                        if (!empty($state['has_tour_guide']) && $state['has_tour_guide']) {
                            $label .= " 👨";
                        }
                        
                        // Add meal abbreviations (BLD)
                        $mealAbbrev = '';
                        if (!empty($state['breakfast'])) {
                            $mealAbbrev .= 'B';
                        }
                        if (!empty($state['lunch'])) {
                            $mealAbbrev .= 'L';
                        }
                        if (!empty($state['dinner'])) {
                            $mealAbbrev .= 'D';
                        }
                        
                        if ($mealAbbrev) {
                            $label .= " ({$mealAbbrev})";
                        }
                        
                        return $label;
                    })
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Select::make('current_city_id')
                        ->label('Current City')
                            ->options(City::getCachedSelectOptions())
                            ->required()
                            ->live(),
                        Select::make('accommodation_city_id')
                            ->label('Accommodation City')
                            ->options(City::getCachedSelectOptions())
                            ->live()
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
                            ->label('Accommodation')
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
                        Toggle::make('has_vehicle')->label('Has Car')
                            ->reactive()
                            ->default(function (callable $get) {
                                // Set default based on existing vehicle data
                                $vehicleMode = $get('vehicle_usage_mode');
                                return !empty($vehicleMode);
                            })
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    // Set to full_day when has_vehicle is true
                                    $set('vehicle_usage_mode', \App\Enums\VehicleUsageModeEnum::FULL_DAY->value);
                                    $set('vehicle_hours', 0);
                                } else {
                                    // Clear vehicle fields when has_vehicle is false
                                    $set('vehicle_usage_mode', null);
                                    $set('vehicle_hours', null);
                                }
                            })
                            ->live(),
                        Toggle::make('has_tour_guide')->label('Has Tour Guide'),
                        Select::make('breakfast')->options(MealType::getCachedSelectOptions())->columnStart(1),
                        Select::make('lunch')->options(MealType::getCachedSelectOptions()),
                        Select::make('dinner')->options(MealType::getCachedSelectOptions()),

                        Tabs::make('activities')->tabs([
                            Tab::make('Attractions')
                                ->badge(function (callable $get) {
                                    $attractions = $get('attractions') ?? [];
                                    return count($attractions);
                                })
                                ->schema([
                                    Repeater::make('attractions')
                                        ->hiddenLabel()
                                        ->defaultItems(0)
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
                                                ->required()
                                                ->label('City')
                                                ->options(City::getCachedSelectOptions())
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    $set('attraction_id', null);
                                                    $set('sub_attractions', []);
                                                }),

                                            Select::make('attraction_id')
                                                ->required()
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
                                                ->visible(fn(callable $get) => !empty($get('attraction_id')) && !$get('is_outview'))
                                                ->disabled(fn(callable $get) => $get('is_outview'))
                                                ->searchable()
                                                ->preload(),
                                        ])
                                        ->addActionLabel('Add Attraction')
                                        ->reorderable()
                                        ->collapsible(),
                                ]),
                            Tab::make('Tickets')
                                ->badge(function (callable $get) {
                                    $tickets = $get('tickets') ?? [];
                                    return count($tickets);
                                })
                                ->schema([
                                    Repeater::make('tickets')
                                        ->defaultItems(0)
                                        ->hiddenLabel()
                                        ->columnStart(1)
                                        ->columnSpanFull()
                                        ->label('Tickets')
                                        ->table([
                                            TableColumn::make('Mode'),
                                            TableColumn::make('From City'),
                                            TableColumn::make('To City'),
                                            TableColumn::make('Number'),
                                            TableColumn::make('Class'),
                                            TableColumn::make('Departure'),
                                            TableColumn::make('Arrival'),
                                        ])
                                        ->schema([
                                            Select::make('transport_mode')
                                                ->options(TransportModeEnum::class)
                                                ->required()
                                                ->label('Mode'),
                                            Select::make('from_city_id')->options(City::getCachedSelectOptions())->label('From City')->required(),
                                            Select::make('to_city_id')->options(City::getCachedSelectOptions())->label('To City')->required(),
                                            TextInput::make('transport_number')->label('Number'),
                                            Select::make('class')->options(TicketClassEnum::class)->label('Class')->required(),
                                            TimePicker::make('departure_time')->label('Departure')->seconds(false),
                                            TimePicker::make('arrival_time')->label('Arrival')->seconds(false),
                                        ])
                                        ->addActionLabel('Add Ticket')
                                        ->reorderable(false)
                                        ->collapsible(),
                                ]),
                            Tab::make('Experiences')
                                ->badge(function (callable $get) {
                                    $experiences = $get('experiences') ?? [];
                                    return count($experiences);
                                })
                                ->schema([
                                    Repeater::make('experiences')
                                        ->defaultItems(0)
                                        ->hiddenLabel()
                                        ->columnStart(1)
                                        ->columnSpanFull()
                                        ->label('Experiences')
                                        ->table([
                                            TableColumn::make('City'),
                                            TableColumn::make('Experience'),
                                        ])
                                        ->schema([
                                            Select::make('city_id')
                                            ->required()
                                                ->label('City')
                                                ->options(City::getCachedSelectOptions())
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    $set('experience_id', null);
                                                }),

                                            Select::make('experience_id')->required()
                                                ->label('Experience')
                                                ->options(function (callable $get) {
                                                    $cityId = $get('city_id');
                                                    if (!$cityId) {
                                                        return [];
                                                    }

                                                    return Experience::where('city_id', $cityId)
                                                        ->pluck('name', 'id')
                                                        ->toArray();
                                                })
                                                ->disabled(fn(callable $get) => empty($get('city_id')))
                                                ->searchable()
                                                ->preload(),
                                        ])
                                        ->addActionLabel('Add Experience')
                                        ->reorderable()
                                        ->collapsible(),
                                            ]),
                            Tab::make('Description')
                                ->badge(function (callable $get) {
                                    $description = $get('description') ?? '';
                                    return !empty(trim($description)) ? '●' : null;
                                })
                                ->schema([
                                Textarea::make('description')->label('Description'),
                            ]),

                        ])->columnStart(1)->columnSpanFull(),


                    ])
                    ->required(),
            ]);
    }
}
