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
                                            ->default(0)
                                            ->rules(['numeric', 'min:0'])
                                            ->validationMessages([
                                                'numeric' => 'Value must be a valid number',
                                                'min' => 'Value cannot be negative',
                                            ]),
                                        TextInput::make('vehicle_half_days_qty')
                                            ->label('Vehicle Half Days')
                                            ->default(0)
                                            ->rules(['numeric', 'min:0'])
                                            ->validationMessages([
                                                'numeric' => 'Value must be a valid number',
                                                'min' => 'Value cannot be negative',
                                            ]),
                                        TextInput::make('vehicle_hours_qty')
                                            ->label('Vehicle Hours')
                                            ->default(0)
                                            ->rules(['numeric', 'min:0'])
                                            ->validationMessages([
                                                'numeric' => 'Value must be a valid number',
                                                'min' => 'Value cannot be negative',
                                            ]),
                                        TextInput::make('vehicle_airport_transfers_qty')
                                            ->label('Airport Transfers')
                                            ->default(0)
                                            ->rules(['numeric', 'min:0'])
                                            ->validationMessages([
                                                'numeric' => 'Value must be a valid number',
                                                'min' => 'Value cannot be negative',
                                            ]),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('driver_base_meal_budget')
                                            ->label('Driver Meal Budget')
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(50)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Driver meal budget is required',
                                                'numeric' => 'Value must be a number',
                                                'min' => 'Value cannot be negative',
                                            ]),
                                        TextInput::make('driver_base_accommodation_budget')
                                            ->label('Driver Accommodation Budget')
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(100)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Driver accommodation budget is required',
                                                'numeric' => 'Value must be a number',
                                                'min' => 'Value cannot be negative',
                                            ]),
                                    ]),
                            ]),
                        Section::make('Vehicle Types')
                            ->schema([
                            Repeater::make('vehicleTypes')
                                    ->hiddenLabel()
                                    ->reorderable(false)
                                    ->deleteAction(
                                        fn (\Filament\Actions\Action $action) => $action
                                            ->disabled(function (array $arguments, Repeater $component, $record) {
                                                // Get vehicle type data
                                                $state = $component->getState();
                                                $vehicleData = $state[$arguments['item']] ?? null;
                                                
                                                if (!$vehicleData || !isset($vehicleData['vehicle_type_id'])) {
                                                    return false;
                                                }
                                                
                                                // Check usage in offers
                                                $quotationItinerary = $record;
                                                $usageCount = \App\Models\Tenants\QuotationOffer::query()
                                                    ->whereHas('quotationOfferGroup', function ($query) use ($quotationItinerary) {
                                                        $query->where('quotation_itinerary_id', $quotationItinerary->id);
                                                    })
                                                    ->where('vehicle_type_id', $vehicleData['vehicle_type_id'])
                                                    ->count();
                                                
                                                return $usageCount > 0; // Disable if used
                                            })
                                            ->tooltip(function (array $arguments, Repeater $component, $record) {
                                                // Get vehicle type data
                                                $state = $component->getState();
                                                $vehicleData = $state[$arguments['item']] ?? null;
                                                
                                                if (!$vehicleData || !isset($vehicleData['vehicle_type_id'])) {
                                                    return null;
                                                }
                                                
                                                // Check usage in offers
                                                $quotationItinerary = $record;
                                                $usageCount = \App\Models\Tenants\QuotationOffer::query()
                                                    ->whereHas('quotationOfferGroup', function ($query) use ($quotationItinerary) {
                                                        $query->where('quotation_itinerary_id', $quotationItinerary->id);
                                                    })
                                                    ->where('vehicle_type_id', $vehicleData['vehicle_type_id'])
                                                    ->count();
                                                
                                                if ($usageCount > 0) {
                                                    $vehicleType = \App\Models\Tenants\VehicleType::find($vehicleData['vehicle_type_id']);
                                                    $vehicleName = $vehicleType ? $vehicleType->name : 'This vehicle type';
                                                    return "{$vehicleName} is used in {$usageCount} offer(s). Please remove it from offers first.";
                                                }
                                                
                                                return null;
                                            })
                                    )
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
                                                    }
                                                } else {
                                                    // Clear prices when vehicle type is removed
                                                    $set('per_day_price', null);
                                                    $set('half_day_price', null);
                                                }
                                            }),
                                        TextInput::make('per_day_price')
                                            ->label('Per Day Price')
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder('Enter price')
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Per day price is required',
                                                'numeric' => 'Price must be a valid number',
                                                'min' => 'Price cannot be negative',
                                            ]),
                                        TextInput::make('half_day_price')
                                            ->label('Half Day Price')
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder('Enter price')
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Half day price is required',
                                                'numeric' => 'Price must be a valid number',
                                                'min' => 'Price cannot be negative',
                                            ]),
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
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if (is_array($value)) {
                                            foreach ($value as $index => $ticket) {
                                                // Create route description for better error messages
                                                $fromCity = \App\Models\Base\City::find($ticket['from_city_id'] ?? null)?->name ?? 'Unknown';
                                                $toCity = \App\Models\Base\City::find($ticket['to_city_id'] ?? null)?->name ?? 'Unknown';
                                                $transportMode = $ticket['transport_mode'] ?? 'Unknown';
                                                $class = $ticket['class'] ?? 'Unknown';
                                                
                                                $routeDescription = "{$fromCity} to {$toCity} ({$transportMode} - {$class})";
                                                
                                                if (!isset($ticket['price']) || $ticket['price'] === null || $ticket['price'] === '') {
                                                    $fail("Ticket price is required for route: {$routeDescription}");
                                                    break;
                                                }
                                                if (!is_numeric($ticket['price']) || $ticket['price'] < 0) {
                                                    $fail("Ticket price must be a valid number (0 or greater) for route: {$routeDescription}");
                                                    break;
                                                }
                                            }
                                        }
                                    };
                                },
                            ])
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
                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                    ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (float)$state)
                                    ->required()
                                    ->rules(['required', 'numeric', 'min:0'])
                                    ->validationMessages([
                                        'required' => 'Ticket price is required',
                                        'numeric' => 'Ticket price must be a number',
                                        'min' => 'Ticket price cannot be negative',
                                    ])
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
                                TextInput::make('price')
                                    ->label('Price')
                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                    ->required()
                                    ->rules(['required', 'numeric', 'min:0'])
                                    ->validationMessages([
                                        'required' => 'Price is required',
                                        'numeric' => 'Price must be a valid number',
                                        'min' => 'Price cannot be negative',
                                    ]),
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
                                TableColumn::make('Accommodation')->width('22%'),
                                TableColumn::make('City')->width('18%'),
                                TableColumn::make('Nights')->width('12%'),
                                TableColumn::make('Breakfast')->width('12%'),
                                TableColumn::make('Room Categories')->width('36%'),
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
                                Toggle::make('has_breakfast')
                                    ->label('Has Breakfast')
                                    ->default(true),
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
                                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                                    ->required()
                                                    ->placeholder('Enter room price')
                                                    ->rules(['required', 'numeric', 'min:0'])
                                                    ->validationMessages([
                                                        'required' => 'Room price is required',
                                                        'numeric' => 'Value must be a valid number',
                                                        'min' => 'Value cannot be negative',
                                                    ]),
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
                                        TableColumn::make('Free for Guide'),
                                        TableColumn::make('Free for Companions'),
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

                                        Toggle::make('is_free_for_guide')
                                            ->label('Free for Guide')
                                            ->dehydrated(),
                                        Toggle::make('is_free_for_other_companions')
                                            ->label('Free for Companions')
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set) {
                                                if ($state) {
                                                    $set('is_free_for_guide', true);
                                                }
                                            })
                                            ->dehydrated(),
                                            TextInput::make('price')
                                        
                                            ->label('Price')
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Price is required',
                                                'numeric' => 'Value must be a valid number',
                                                'min' => 'Value cannot be negative',
                                            ]),
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
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Entry price is required',
                                                'numeric' => 'Value must be a valid number',
                                                'min' => 'Value cannot be negative',
                                            ])
                                            ->disabled(fn($get) => $get('is_outview') == true),
                                        Toggle::make('is_outview')
                                            ->label('Outview')
                                            ->disabled()
                                            ->reactive(),
                                        Repeater::make('subAttractions')
                                            ->addable(false)
                                            ->deletable(false)
                                            ->reorderable(false)
                                            ->label('Sub Attractions')
                                            ->hidden(fn($get) => $get('is_outview') == true)
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
                                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                                            ->required()
                                                            ->placeholder('Enter price')
                                                            ->rules(['required', 'numeric', 'min:0'])
                                                            ->validationMessages([
                                                                'required' => 'Price is required',
                                                                'numeric' => 'Value must be a valid number',
                                                                'min' => 'Value cannot be negative',
                                                            ]),
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
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(50)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Companion meal budget is required',
                                                'numeric' => 'Value must be a number',
                                                'min' => 'Value cannot be negative',
                                            ]),
                                        TextInput::make('companion_base_accommodation_budget')
                                            ->label('Companion Accommodation Budget')
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(100)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Companion accommodation budget is required',
                                                'numeric' => 'Value must be a number',
                                                'min' => 'Value cannot be negative',
                                            ]),
                                    ]),
                            ]),
                        Section::make('Companions')
                            ->schema([
                            Repeater::make('companions')
                                    ->hiddenLabel()
                                    ->reorderable(false)
                                    ->deleteAction(
                                        fn (\Filament\Actions\Action $action) => $action
                                            ->disabled(function (array $arguments, Repeater $component, $record) {
                                                // Get companion data
                                                $state = $component->getState();
                                                $companionData = $state[$arguments['item']] ?? null;
                                                
                                                if (!$companionData || !isset($companionData['companion_type_id'])) {
                                                    return false;
                                                }
                                                
                                                // Check usage in offer groups
                                                $quotationItinerary = $record;
                                                $usageCount = \App\Models\Tenants\QuotationOfferGroupCompanion::query()
                                                    ->whereHas('quotationOfferGroup', function ($query) use ($quotationItinerary) {
                                                        $query->where('quotation_itinerary_id', $quotationItinerary->id);
                                                    })
                                                    ->where('companion_type_id', $companionData['companion_type_id'])
                                                    ->count();
                                                
                                                return $usageCount > 0; // Disable if used
                                            })
                                            ->tooltip(function (array $arguments, Repeater $component, $record) {
                                                // Get companion data
                                                $state = $component->getState();
                                                $companionData = $state[$arguments['item']] ?? null;
                                                
                                                if (!$companionData || !isset($companionData['companion_type_id'])) {
                                                    return null;
                                                }
                                                
                                                // Check usage in offer groups
                                                $quotationItinerary = $record;
                                                $usageCount = \App\Models\Tenants\QuotationOfferGroupCompanion::query()
                                                    ->whereHas('quotationOfferGroup', function ($query) use ($quotationItinerary) {
                                                        $query->where('quotation_itinerary_id', $quotationItinerary->id);
                                                    })
                                                    ->where('companion_type_id', $companionData['companion_type_id'])
                                                    ->count();
                                                
                                                if ($usageCount > 0) {
                                                    $companionType = \App\Models\Tenants\CompanionType::find($companionData['companion_type_id']);
                                                    $companionName = $companionType ? $companionType->name : 'This companion';
                                                    return "{$companionName} is used in {$usageCount} offer group(s). Please remove it from offer groups first.";
                                                }
                                                
                                                return null;
                                            })
                                    )
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
                                                    $set('per_day_price', null);
                                                    $set('half_day_price', null);
                                                    $set('per_hour_price', null);
                                                }
                                            }),
                                        TextInput::make('per_day_price')
                                            ->label('Per Day Price')
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder('Enter price')
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Per day price is required',
                                                'numeric' => 'Price must be a valid number',
                                                'min' => 'Price cannot be negative',
                                            ]),
                                        TextInput::make('half_day_price')
                                            ->label('Half Day Price')
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder('Enter price')
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Half day price is required',
                                                'numeric' => 'Price must be a valid number',
                                                'min' => 'Price cannot be negative',
                                            ]),
                                        TextInput::make('per_hour_price')
                                            ->label('Per Hour Price')
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder('Enter price')
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => 'Per hour price is required',
                                                'numeric' => 'Price must be a valid number',
                                                'min' => 'Price cannot be negative',
                                            ]),
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
                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                    ->required()
                                    ->rules(['required', 'numeric', 'min:0'])
                                    ->validationMessages([
                                        'required' => 'Price is required',
                                        'numeric' => 'Price must be a valid number',
                                        'min' => 'Price cannot be negative',
                                    ]),
                            ])
                            ->addActionLabel('Add Expense')
                            ->collapsible(),
                    ]),
                ])->columnSpanFull()->skippable()
            ]);
        }
    }
