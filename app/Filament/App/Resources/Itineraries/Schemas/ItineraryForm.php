<?php

namespace App\Filament\App\Resources\Itineraries\Schemas;

use App\Enums\HireModeEnum;
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
                    ->label(__('app-itineraries.fields.days'))
                    ->live()
                    ->itemLabel(function (array $state, $component) {
                        // Get day number using a different approach
                        $dayNumber = 1;
                        
                        // Try to get from state path first
                        $statePath = $component->getStatePath();
                        if ($statePath) {
                            $pathParts = explode('.', $statePath);
                            if (count($pathParts) >= 2 && is_numeric($pathParts[1])) {
                                $dayNumber = (int) $pathParts[1] + 1;
                            }
                        }
                        
                        // Alternative: try to get from container state
                        if ($dayNumber === 1) {
                            $container = $component->getContainer();
                            if ($container) {
                                $allDays = $container->getState();
                                if (is_array($allDays)) {
                                    $currentIndex = array_search($state, $allDays, true);
                                    if ($currentIndex !== false) {
                                        $dayNumber = $currentIndex + 1;
                                    }
                                }
                            }
                        }
                        
                        // Fallback: use a static counter
                        static $counter = 0;
                        if ($dayNumber === 1) {
                            $counter++;
                            $dayNumber = $counter;
                        }
                        
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
                        
                        $label = __('app-itineraries.fields.day') . " {$dayNumber}";
                        
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
                        
                        // Add companion icon
                        if (!empty($state['has_companion']) && $state['has_companion']) {
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
                        ->label(__('app-itineraries.fields.current_city'))
                            ->options(City::getCachedSelectOptionsForTenant())
                            ->live()
                            ->placeholder(__('app-itineraries.placeholders.select_city')),
                        Select::make('accommodation_city_id')
                            ->label(__('app-itineraries.fields.accommodation_city'))
                            ->options(City::getCachedSelectOptionsForTenant())
                            ->live()
                            ->placeholder(__('app-itineraries.placeholders.select_accommodation_city'))
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('accommodation_id', null);
                            }),
                        Select::make('accommodation_star_rating')
                            ->options(StarRatingEnum::getOptions())
                            ->label(__('app-itineraries.fields.star_rating'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('accommodation_id', null);
                            }),
                        Select::make('accommodation_id')
                            ->label(__('app-itineraries.fields.accommodation'))
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
                            ->hidden(fn($get) => empty($get('accommodation_city_id')))
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // اگر accommodation انتخاب شد و star rating خالی بود
                                if ($state && !$get('accommodation_star_rating')) {
                                    $accommodation = Accommodation::find($state);
                                    if ($accommodation?->star_rating) {
                                        $set('accommodation_star_rating', $accommodation->star_rating);
                                    }
                                }
                            }),
                        Toggle::make('has_vehicle')->label(__('app-itineraries.fields.has_car'))
                            ->reactive()
                            ->columnStart(1)
                            ->default(function (callable $get) {
                                // Set default based on existing vehicle data
                                $vehicleMode = $get('vehicle_usage_mode');
                                return !empty($vehicleMode);
                            })
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    // Set to full_day when has_vehicle is true
                                    $set('vehicle_usage_mode', VehicleUsageModeEnum::FULL_DAY->value);
                                    $set('vehicle_hours', 0);
                                } else {
                                    // Clear vehicle fields when has_vehicle is false
                                    $set('vehicle_usage_mode', null);
                                    $set('vehicle_hours', null);
                                }
                            })
                            ->live(),
                        Toggle::make('has_companion')
                            ->label(__('app-itineraries.fields.has_companion'))
                            ->default(false)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('companion_hire_mode', 'daily');
                                } else {
                                    $set('companion_hire_mode', null);
                                    $set('companion_hours', null);
                                }
                            })
                            ->live(),
                        Select::make('breakfast')->label(__('common-fields.breakfast'))->options(MealType::getCachedSelectOptions())->columnStart(1),
                        Select::make('lunch')->label(__('common-fields.lunch'))->options(MealType::getCachedSelectOptions()),
                        Select::make('dinner')->label(__('common-fields.dinner'))->options(MealType::getCachedSelectOptions()),

                        Tabs::make('activities')->tabs([
                            Tab::make(__('app-itineraries.tabs.attractions'))
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
                                        ->label(__('app-itineraries.tabs.attractions'))
                                        ->table([
                                            TableColumn::make(__('app-itineraries.table_columns.city')),
                                            TableColumn::make(__('app-itineraries.table_columns.attraction')),
                                            TableColumn::make(__('app-itineraries.table_columns.outview')),
                                            TableColumn::make(__('app-itineraries.table_columns.sub_attractions')),
                                        ])
                                        ->schema([
                                            Select::make('city_id')
                                                ->required()
                                                ->label(__('common-fields.city'))
                                                ->options(City::getCachedSelectOptionsForTenant())
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    $set('attraction_id', null);
                                                    $set('sub_attractions', []);
                                                }),

                                            Select::make('attraction_id')
                                                ->label(__('app-itineraries.fields.main_attraction'))
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
                                                })
                                                ->rules(['required_with:city_id']),
                                            Toggle::make('is_outview')
                                                ->label(__('app-itineraries.fields.outview'))
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    // اگر outview فعال شد، sub_attractions رو پاک کن
                                                    if ($state) {
                                                        $set('sub_attractions', []);
                                                    }
                                                }),

                                            Select::make('sub_attractions')
                                                ->label(__('app-itineraries.fields.sub_attractions'))
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
                                        ->addActionLabel(__('app-itineraries.actions.add_attraction'))
                                        ->reorderable()
                                        ->collapsible(),
                                ]),
                            Tab::make(__('app-itineraries.tabs.tickets'))
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
                                        ->label(__('app-itineraries.tabs.tickets'))
                                        ->table([
                                            TableColumn::make(__('app-itineraries.table_columns.mode')),
                                            TableColumn::make(__('app-itineraries.table_columns.from_city')),
                                            TableColumn::make(__('app-itineraries.table_columns.to_city')),
                                            TableColumn::make(__('app-itineraries.table_columns.number')),
                                            TableColumn::make(__('app-itineraries.table_columns.class')),
                                            TableColumn::make(__('app-itineraries.table_columns.departure')),
                                            TableColumn::make(__('app-itineraries.table_columns.arrival')),
                                        ])
                                        ->schema([
                                            Select::make('transport_mode')
                                                ->options(TransportModeEnum::class)
                                                ->label(__('app-itineraries.fields.mode'))
                                                ->rules(['required_with:from_city_id']),
                                            Select::make('from_city_id')
                                                ->options(City::getCachedSelectOptionsForTenant())
                                                ->label(__('app-itineraries.fields.from_city'))
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    $set('to_city_id', null);
                                                }),
                                            Select::make('to_city_id')
                                                ->options(City::getCachedSelectOptionsForTenant())
                                                ->label(__('app-itineraries.fields.to_city'))
                                                ->rules(['required_with:from_city_id']),
                                            TextInput::make('transport_number')->label(__('app-itineraries.fields.transport_number')),
                                            Select::make('class')
                                                ->options(TicketClassEnum::class)
                                                ->label(__('app-itineraries.fields.class'))
                                                ->rules(['required_with:from_city_id']),
                                            TimePicker::make('departure_time')->label(__('app-itineraries.fields.departure'))->seconds(false),
                                            TimePicker::make('arrival_time')->label(__('app-itineraries.fields.arrival'))->seconds(false),
                                        ])
                                        ->addActionLabel(__('app-itineraries.actions.add_ticket'))
                                        ->reorderable(false)
                                        ->collapsible(),
                                ]),
                            Tab::make(__('app-itineraries.tabs.experiences'))
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
                                        ->label(__('app-itineraries.tabs.experiences'))
                                        ->table([
                                            TableColumn::make(__('app-itineraries.table_columns.city')),
                                            TableColumn::make(__('app-itineraries.table_columns.experience')),
                                        ])
                                        ->schema([
                                            Select::make('city_id')
                                            ->required()
                                                ->label(__('common-fields.city'))
                                                ->options(City::getCachedSelectOptionsForTenant())
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    $set('experience_id', null);
                                                }),

                                            Select::make('experience_id')
                                                ->label(__('app-itineraries.table_columns.experience'))
                                                ->rules(['required_with:city_id'])
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
                                        ->addActionLabel(__('app-itineraries.actions.add_experience'))
                                        ->reorderable()
                                        ->collapsible(),
                                            ]),
                            Tab::make(__('app-itineraries.tabs.description'))
                                ->badge(function (callable $get) {
                                    $description = $get('description') ?? '';
                                    return !empty(trim($description)) ? '●' : null;
                                })
                                ->schema([
                                Textarea::make('description')->label(__('app-itineraries.fields.description')),
                            ]),

                        ])->columnStart(1)->columnSpanFull(),


                    ])
                    ->required(),
            ]);
    }
}
