<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;

class BreakdownForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Wizard\Step::make('Vehicle Types')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        Section::make('Vehicle Quantities')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('vehicle_days_qty')
                                            ->label('Vehicle Days')
                                            ->numeric()
                                            ->default(0),
                                        TextInput::make('vehicle_half_days_qty')
                                            ->label('Vehicle Half Days')
                                            ->numeric()
                                            ->default(0),
                                        TextInput::make('vehicle_hours_qty')
                                            ->label('Vehicle Hours')
                                            ->numeric()
                                            ->default(0),
                                        TextInput::make('vehicle_airport_transfers_qty')
                                            ->label('Airport Transfers')
                                            ->numeric()
                                            ->default(0),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('driver_base_meal_budget')
                                            ->label('Driver Meal Budget')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(50),
                                        TextInput::make('driver_base_accommodation_budget')
                                            ->label('Driver Accommodation Budget')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(100),
                                    ]),
                            ]),
                        Section::make('Vehicle Types')
                            ->schema([
                            Repeater::make('vehicleTypes')
                                    ->hiddenLabel()
                                    ->reorderable(false)
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                if (is_array($value)) {
                                                    // Count occurrences of each vehicle_type_id
                                                    $vehicleTypeIds = collect($value)->pluck('vehicle_type_id')->filter()->toArray();
                                                    $counts = array_count_values($vehicleTypeIds);
                                                    
                                                    foreach ($counts as $vehicleTypeId => $count) {
                                                        if ($count > 1) {
                                                            $vehicleType = \App\Models\Tenants\VehicleType::find($vehicleTypeId);
                                                            $vehicleTypeName = $vehicleType ? $vehicleType->name : 'Unknown';
                                                            $fail("Vehicle type '{$vehicleTypeName}' is selected multiple times. Each vehicle type can only be selected once.");
                                                            break;
                                                        }
                                                    }
                                                }
                                            };
                                        },
                                    ])
                                    ->table([
                                        TableColumn::make('Vehicle Type'),
                                        TableColumn::make('Per Day Price'),
                                        TableColumn::make('Half Day Price'),
                                        TableColumn::make('Extra Hour Price'),
                                    ])
                                    ->schema([
                                        Select::make('vehicle_type_id')
                                            ->label('Vehicle Type')
                                            ->options(\App\Models\Tenants\VehicleType::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $vehicleType = \App\Models\Tenants\VehicleType::find($state);
                                                    if ($vehicleType) {
                                                        $set('per_day_price', $vehicleType->per_day_price ?? 0);
                                                        $set('half_day_price', $vehicleType->half_day_price ?? 0);
                                                        $set('extra_hour_price', $vehicleType->extra_hour_price ?? 0);
                                                    }
                                                } else {
                                                    // Clear prices when vehicle type is removed
                                                    $set('per_day_price', null);
                                                    $set('half_day_price', null);
                                                    $set('extra_hour_price', null);
                                                }
                                            }),
                                        TextInput::make('per_day_price')
                                            ->label('Per Day Price')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0),
                                        TextInput::make('half_day_price')
                                            ->label('Half Day Price')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0),
                                        TextInput::make('extra_hour_price')
                                            ->label('Extra Hour Price')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0),
                                    ])
                                    ->addActionLabel('Add Vehicle Type')
                                    ->collapsible(),
                            ]),
                    ]),

                Wizard\Step::make('Tickets')
                    ->icon('heroicon-o-ticket')
                    
                    ->schema([
                        Repeater::make('tickets')
                            ->hiddenLabel()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->table([
                                TableColumn::make('Transport Mode'),
                                TableColumn::make('Class'),
                                TableColumn::make('From City'),
                                TableColumn::make('To City'),
                                TableColumn::make('Price'),
                            ])
                            ->schema([
                                Select::make('transport_mode')
                                    ->disabled()
                                    ->label('Transport Mode')
                                    ->options([
                                        'air' => 'Air',
                                        'train' => 'Train',
                                        'land' => 'Land',
                                    ])
                                    ->required()
                                    ->dehydrated(),
                                Select::make('class')
                                    ->disabled()
                                    ->label('Class')
                                    ->options([
                                        'economy' => 'Economy',
                                        'business' => 'Business',
                                        'first' => 'First',
                                    ])
                                    ->required()
                                    ->dehydrated(),
                                Select::make('from_city_id')
                                    ->disabled()
                                    ->label('From City')
                                    ->options(\App\Models\Base\City::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->dehydrated(),
                                Select::make('to_city_id')
                                    ->disabled()
                                    ->label('To City')
                                    ->options(\App\Models\Base\City::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->dehydrated(),
                                TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                    ->default(0),
                            ])
                            ->addActionLabel('Add Ticket')
                            ->collapsible(),
                    ]),

                Wizard\Step::make('Meals')
                    ->icon('heroicon-o-cake')
                    
                    ->schema([
                        Repeater::make('meals')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Meal Type'),
                                TableColumn::make('Quantity'),
                                TableColumn::make('Price'),
                            ])
                            ->schema([
                                Select::make('meal_type_id')
                                    ->label('Meal Type')
                                    ->disabled()
                                    ->options(\App\Models\Tenants\MealType::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->dehydrated(),
                                TextInput::make('qty')
                                    ->disabled()
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->dehydrated(),
                                TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                    ->default(0),
                            ])
                            ->addActionLabel('Add Meal')
                            ->collapsible(),
                    ]),

                Wizard\Step::make('Hotels')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Repeater::make('accommodations')
                            ->hiddenLabel()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->table([
                                TableColumn::make('Accommodation')->width('25%'),
                                TableColumn::make('City')->width('20%'),
                                TableColumn::make('Nights')->width('15%'),
                                TableColumn::make('Room Categories')->width('40%'),
                            ])
                            ->schema([
                                Select::make('accommodation_id')
                                    ->label('Accommodation')
                                    ->options(\App\Models\Base\Accommodation::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                                Select::make('city_id')
                                    ->label('City')
                                    ->options(\App\Models\Base\City::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('nights_qty')
                                    ->label('Nights')
                                    ->numeric()
                                    ->disabled()
                                    ->default(1)
                                    ->dehydrated(),
                                Repeater::make('rooms')
                                    ->label('Room Categories')
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->table([
                                        TableColumn::make('Room Category')->width('60%'),
                                        TableColumn::make('Price')->width('40%'),
                                    ])
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('room_category_id')
                                                    ->label('Room Category')
                                                    ->options(\App\Models\Base\RoomCategory::pluck('name', 'id'))
                                                    ->searchable()
                                                    ->required()
                                                    ->disabled()
                                                    ->dehydrated(),
                                                TextInput::make('price')
                                                    ->label('Price')
                                                    ->numeric()
                                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                                    ->default(0),
                                            ])
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->addable(false)
                            ->deletable(false),
                    ]),

                Wizard\Step::make('Attractions')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Section::make('Experiences')
                            ->schema([
                            Repeater::make('experiences')
                                    ->hiddenLabel()
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->table([
                                        TableColumn::make('Experience'),
                                        TableColumn::make('Charge Mode'),
                                        TableColumn::make('Price'),
                                    ])
                                    ->schema([
                                        Select::make('experience_id')
                                            ->label('Experience')
                                            ->disabled()
                                            ->options(\App\Models\Tenants\Experience::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->dehydrated(),
                                        Select::make('charge_mode')
                                            ->disabled()
                                            ->label('Charge Mode')
                                            ->options([
                                                'per_person' => 'Per Person',
                                                'per_group' => 'Per Group',
                                                'per_hour' => 'Per Hour',
                                            ])
                                            ->required()
                                            ->dehydrated(),
                                        TextInput::make('price')
                                        
                                            ->label('Price')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0),
                                    ])
                                    ->addActionLabel('Add Experience')
                                    ->collapsible(),
                            ]),
                        Section::make('Attractions')
                            ->schema([
                            Repeater::make('attractions')
                                    ->hiddenLabel()
                                    ->table([
                                        TableColumn::make('Attraction')->width('20%'),
                                        TableColumn::make('City')->width('15%'),
                                        TableColumn::make('Entry Price')->width('15%'),
                                        TableColumn::make('Outview')->width('10%'),
                                        TableColumn::make('Sub Attractions')->width('40%'),
                                    ])
                                    ->schema([
                                        Select::make('attraction_id')
                                            ->label('Attraction')
                                            ->options(\App\Models\Base\Attraction::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->disabled()
                                            ->dehydrated(),
                                        Select::make('city_id')
                                            ->label('City')
                                            ->options(\App\Models\Base\City::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->disabled()
                                            ->dehydrated(),
                                        TextInput::make('entry_price')
                                            ->label('Entry Price')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0),
                                        Toggle::make('is_outview')
                                            ->label('Outview')
                                            ->disabled(),
                                        Repeater::make('subAttractions')
                                            ->label('Sub Attractions')
                                            ->table([
                                                TableColumn::make('Sub Attraction')->width('60%'),
                                                TableColumn::make('Price')->width('40%'),
                                            ])
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('sub_attraction_id')
                                                            ->label('Sub Attraction')
                                                            ->options(\App\Models\Base\SubAttraction::pluck('name', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->disabled()
                                                            ->dehydrated(),
                                                        TextInput::make('price')
                                                            ->label('Price')
                                                            ->numeric()
                                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                                            ->default(0),
                                                    ])
                                            ])->addable(false)->deletable(false)
                                            ->columnSpanFull(),
                                    ])->addable(false)->deletable(false),
                            ]),
                    ]),

                Wizard\Step::make('Companions')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Section::make('Companion Budgets')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('companion_base_meal_budget')
                                            ->label('Companion Meal Budget')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(50),
                                        TextInput::make('companion_base_accommodation_budget')
                                            ->label('Companion Accommodation Budget')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(100),
                                    ]),
                            ]),
                        Section::make('Companions')
                            ->schema([
                            Repeater::make('companions')
                                    ->hiddenLabel()
                                    ->reorderable(false)
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                if (is_array($value)) {
                                                    // Count occurrences of each companion_type_id
                                                    $companionTypeIds = collect($value)->pluck('companion_type_id')->filter()->toArray();
                                                    $counts = array_count_values($companionTypeIds);
                                                    
                                                    foreach ($counts as $companionTypeId => $count) {
                                                        if ($count > 1) {
                                                            $companionType = \App\Models\Tenants\CompanionType::find($companionTypeId);
                                                            $companionTypeName = $companionType ? $companionType->name : 'Unknown';
                                                            $fail("Companion type '{$companionTypeName}' is selected multiple times. Each companion type can only be selected once.");
                                                            break;
                                                        }
                                                    }
                                                }
                                            };
                                        },
                                    ])
                                    ->table([
                                        TableColumn::make('Companion Type'),
                                        TableColumn::make('Per Day Price'),
                                        TableColumn::make('Half Day Price'),
                                        TableColumn::make('Per Hour Price'),
                                    ])
                                    ->schema([
                                        Select::make('companion_type_id')
                                            ->label('Companion Type')
                                            ->options(\App\Models\Tenants\CompanionType::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $companionType = \App\Models\Tenants\CompanionType::find($state);
                                                    if ($companionType) {
                                                        $set('per_day_price', $companionType->per_day_price ?? 0);
                                                        $set('half_day_price', $companionType->half_day_price ?? 0);
                                                        $set('per_hour_price', $companionType->per_hour_price ?? 0);
                                                    }
                                                } else {
                                                    // Clear prices when companion type is removed
                                                    $set('per_day_price', 0);
                                                    $set('half_day_price', 0);
                                                    $set('per_hour_price', 0);
                                                }
                                            }),
                                        TextInput::make('per_day_price')
                                            ->label('Per Day Price')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0),
                                        TextInput::make('half_day_price')
                                            ->label('Half Day Price')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0),
                                        TextInput::make('per_hour_price')
                                            ->label('Per Hour Price')
                                            ->numeric()
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0),
                                    ])
                                    ->addActionLabel('Add Companion')
                                    ->collapsible(),
                            ]),
                    ]),

                Wizard\Step::make('Expenses')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Repeater::make('expenses')
                            ->hiddenLabel()
                            ->reorderable(false)
                            ->table([
                                TableColumn::make('Description'),
                                TableColumn::make('Charge Mode'),
                                TableColumn::make('Price'),
                            ])
                            ->schema([
                                TextInput::make('description')
                                    ->label('Description')
                                    ->required(),
                                Select::make('charge_mode')
                                    ->label('Charge Mode')
                                    ->options(\App\Enums\ChargeModeEnum::getOptions())
                                    ->required(),
                                TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                    ->default(0),
                            ])
                            ->addActionLabel('Add Expense')
                            ->collapsible(),
                    ]),
                ])->columnSpanFull()->skippable()
            ]);
        }
    }
