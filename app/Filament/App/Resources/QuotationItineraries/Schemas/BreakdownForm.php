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
                Wizard\Step::make(__('breakdown-form.steps.vehicle_types'))
                    ->icon('heroicon-o-truck')
                    ->schema([
                        Section::make(__('breakdown-form.vehicle_types.quantities_section'))
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('vehicle_days_qty')
                                            ->label(__('breakdown-form.vehicle_types.vehicle_days'))
                                            ->default(0)
                                            ->rules(['numeric', 'min:0'])
                                            ->validationMessages([
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                        TextInput::make('vehicle_half_days_qty')
                                            ->label(__('breakdown-form.vehicle_types.vehicle_half_days'))
                                            ->default(0)
                                            ->rules(['numeric', 'min:0'])
                                            ->validationMessages([
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                        TextInput::make('vehicle_hours_qty')
                                            ->label(__('breakdown-form.vehicle_types.vehicle_hours'))
                                            ->default(0)
                                            ->rules(['numeric', 'min:0'])
                                            ->validationMessages([
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                        TextInput::make('vehicle_airport_transfers_qty')
                                            ->label(__('breakdown-form.vehicle_types.airport_transfers'))
                                            ->default(0)
                                            ->rules(['numeric', 'min:0'])
                                            ->validationMessages([
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('driver_base_meal_budget')
                                            ->label(__('breakdown-form.vehicle_types.driver_meal_budget'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(50)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.driver_meal_budget'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                        TextInput::make('driver_base_accommodation_budget')
                                            ->label(__('breakdown-form.vehicle_types.driver_accommodation_budget'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(100)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.driver_accommodation_budget'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                    ]),
                            ]),
                        Section::make(__('breakdown-form.vehicle_types.section_title'))
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
                                                    return __('breakdown-form.vehicle_types.cannot_delete_in_use', ['count' => $usageCount]);
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
                                        TableColumn::make(__('breakdown-form.vehicle_types.vehicle_type')),
                                        TableColumn::make(__('breakdown-form.vehicle_types.per_day_price')),
                                        TableColumn::make(__('breakdown-form.vehicle_types.half_day_price')),
                                    ])
                                    ->schema([
                                        Select::make('vehicle_type_id')
                                            ->label(__('breakdown-form.vehicle_types.vehicle_type'))
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
                                            ->label(__('breakdown-form.vehicle_types.per_day_price'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder(__('breakdown-form.placeholders.enter_price'))
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.price'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                        TextInput::make('half_day_price')
                                            ->label(__('breakdown-form.vehicle_types.half_day_price'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder(__('breakdown-form.placeholders.enter_price'))
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.price'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                    ])
                                    ->addActionLabel(__('common-fields.add'))
                                    ->collapsible(),
                            ]),
                    ]),

                Wizard\Step::make(__('breakdown-form.steps.tickets'))
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
                                TableColumn::make(__('breakdown-form.tickets.transport_mode')),
                                TableColumn::make(__('breakdown-form.tickets.class')),
                                TableColumn::make(__('breakdown-form.tickets.from_city')),
                                TableColumn::make(__('breakdown-form.tickets.to_city')),
                                TableColumn::make(__('breakdown-form.tickets.price')),
                            ])
                            ->schema([
                                Select::make('transport_mode')
                                    ->disabled()
                                    ->label(__('breakdown-form.tickets.transport_mode'))
                                    ->options(\App\Enums\TransportModeEnum::getOptions())
                                    ->required()
                                    ->dehydrated(),
                                Select::make('class')
                                    ->disabled()
                                    ->label(__('breakdown-form.tickets.class'))
                                    ->options(\App\Enums\TicketClassEnum::getOptions())
                                    ->required()
                                    ->dehydrated(),
                                Select::make('from_city_id')
                                    ->disabled()
                                    ->label(__('breakdown-form.tickets.from_city'))
                                    ->options(\App\Models\Base\City::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->dehydrated(),
                                Select::make('to_city_id')
                                    ->disabled()
                                    ->label(__('breakdown-form.tickets.to_city'))
                                    ->options(\App\Models\Base\City::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->dehydrated(),
                                TextInput::make('price')
                                    ->label(__('breakdown-form.tickets.price'))
                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                    ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (float)$state)
                                    ->required()
                                    ->rules(['required', 'numeric', 'min:0'])
                                    ->validationMessages([
                                        'required' => __('breakdown-form.validation.required_field.price'),
                                        'numeric' => __('breakdown-form.validation.numeric'),
                                        'min' => __('breakdown-form.validation.min'),
                                    ])
                                    ->default(0),
                            ])
                            ->addActionLabel(__('common-fields.add'))
                            ->collapsible(),
                    ]),

                Wizard\Step::make(__('breakdown-form.steps.meals'))
                    ->icon('heroicon-o-cake')
                    
                    ->schema([
                        Repeater::make('meals')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make(__('breakdown-form.meals.meal_type')),
                                TableColumn::make(__('breakdown-form.meals.price')),
                            ])
                            ->schema([
                                Select::make('meal_type_id')
                                    ->label(__('breakdown-form.meals.meal_type'))
                                    ->disabled()
                                    ->options(\App\Models\Tenants\MealType::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->dehydrated(),
                                TextInput::make('price')
                                    ->label(__('breakdown-form.meals.price'))
                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                    ->required()
                                    ->rules(['required', 'numeric', 'min:0'])
                                    ->validationMessages([
                                        'required' => __('breakdown-form.validation.required_field.price'),
                                        'numeric' => __('breakdown-form.validation.numeric'),
                                        'min' => __('breakdown-form.validation.min'),
                                    ]),
                            ])
                            ->addActionLabel(__('common-fields.add'))
                            ->collapsible(),
                    ]),

                Wizard\Step::make(__('breakdown-form.steps.hotels'))
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Repeater::make('accommodations')
                            ->hiddenLabel()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->table([
                                TableColumn::make(__('breakdown-form.hotels.accommodation'))->width('22%'),
                                TableColumn::make(__('breakdown-form.hotels.city'))->width('18%'),
                                TableColumn::make(__('breakdown-form.hotels.nights'))->width('12%'),
                                TableColumn::make(__('common-fields.breakfast'))->width('12%'),
                                TableColumn::make(__('breakdown-form.hotels.room_categories'))->width('36%'),
                            ])
                            ->schema([
                                Select::make('accommodation_id')
                                    ->label(__('breakdown-form.hotels.accommodation'))
                                    ->options(\App\Models\Base\Accommodation::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                                Select::make('city_id')
                                    ->label(__('breakdown-form.hotels.city'))
                                    ->options(\App\Models\Base\City::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('nights_qty')
                                    ->label(__('breakdown-form.hotels.nights'))
                                    ->numeric()
                                    ->disabled()
                                    ->default(1)
                                    ->dehydrated(),
                                Toggle::make('has_breakfast')
                                    ->label(__('breakdown-form.hotels.has_breakfast'))
                                    ->default(true),
                                Repeater::make('rooms')
                                    ->label(__('breakdown-form.hotels.room_categories'))
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->table([
                                        TableColumn::make(__('breakdown-form.hotels.room_category'))->width('60%'),
                                        TableColumn::make(__('breakdown-form.hotels.price'))->width('40%'),
                                    ])
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('room_category_id')
                                                    ->label(__('breakdown-form.hotels.room_category'))
                                                    ->options(\App\Models\Base\RoomCategory::pluck('name', 'id'))
                                                    ->searchable()
                                                    ->required()
                                                    ->disabled()
                                                    ->dehydrated(),
                                                TextInput::make('price')
                                                    ->label(__('breakdown-form.hotels.price'))
                                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                                    ->required()
                                                    ->placeholder(__('breakdown-form.placeholders.enter_price'))
                                                    ->rules(['required', 'numeric', 'min:0'])
                                                    ->validationMessages([
                                                        'required' => __('breakdown-form.validation.required_field.price'),
                                                        'numeric' => __('breakdown-form.validation.numeric'),
                                                        'min' => __('breakdown-form.validation.min'),
                                                    ]),
                                            ])
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->addable(false)
                            ->deletable(false),
                    ]),

                Wizard\Step::make(__('breakdown-form.steps.attractions'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Section::make(__('breakdown-form.attractions.experiences_section'))
                            ->schema([
                            Repeater::make('experiences')
                                    ->hiddenLabel()
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->table([
                                        TableColumn::make(__('breakdown-form.attractions.experience')),
                                        TableColumn::make(__('breakdown-form.attractions.charge_mode')),
                                        TableColumn::make(__('breakdown-form.attractions.free_for_guide')),
                                        TableColumn::make(__('breakdown-form.attractions.free_for_companions')),
                                        TableColumn::make(__('breakdown-form.attractions.price')),

                                    ])
                                    ->schema([
                                        Select::make('experience_id')
                                            ->label(__('breakdown-form.attractions.experience'))
                                            ->disabled()
                                            ->options(\App\Models\Tenants\Experience::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->dehydrated(),
                                        Select::make('charge_mode')
                                            ->disabled()
                                            ->label(__('breakdown-form.attractions.charge_mode'))
                                            ->options(\App\Enums\ChargeModeEnum::getOptions())
                                            ->required()
                                            ->dehydrated(),

                                        Toggle::make('is_free_for_guide')
                                            ->label(__('breakdown-form.attractions.free_for_guide'))
                                            ->dehydrated(),
                                        Toggle::make('is_free_for_other_companions')
                                            ->label(__('breakdown-form.attractions.free_for_companions'))
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set) {
                                                if ($state) {
                                                    $set('is_free_for_guide', true);
                                                }
                                            })
                                            ->dehydrated(),
                                            TextInput::make('price')
                                        
                                            ->label(__('breakdown-form.attractions.price'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.price'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                    ])
                                    ->addActionLabel(__('common-fields.add'))
                                    ->collapsible(),
                            ]),
                        Section::make(__('breakdown-form.attractions.section_title'))
                            ->schema([
                            Repeater::make('attractions')
                                    ->hiddenLabel()
                                    ->table([
                                        TableColumn::make(__('breakdown-form.attractions.attraction'))->width('20%'),
                                        TableColumn::make(__('breakdown-form.attractions.city'))->width('15%'),
                                        TableColumn::make(__('breakdown-form.attractions.entry_price'))->width('15%'),
                                        TableColumn::make(__('breakdown-form.attractions.outview'))->width('10%'),
                                        TableColumn::make(__('breakdown-form.attractions.sub_attractions'))->width('40%'),
                                    ])
                                    ->schema([
                                        Select::make('attraction_id')
                                            ->label(__('breakdown-form.attractions.attraction'))
                                            ->options(\App\Models\Base\Attraction::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->disabled()
                                            ->dehydrated(),
                                        Select::make('city_id')
                                            ->label(__('breakdown-form.attractions.city'))
                                            ->options(\App\Models\Base\City::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->disabled()
                                            ->dehydrated(),
                                        TextInput::make('entry_price')
                                            ->label(__('breakdown-form.attractions.entry_price'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(0)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.entry_price'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ])
                                            ->disabled(fn($get) => $get('is_outview') == true),
                                        Toggle::make('is_outview')
                                            ->label(__('breakdown-form.attractions.outview'))
                                            ->disabled()
                                            ->reactive(),
                                        Repeater::make('subAttractions')
                                            ->addable(false)
                                            ->deletable(false)
                                            ->reorderable(false)
                                            ->label(__('breakdown-form.attractions.sub_attractions'))
                                            ->hidden(fn($get) => $get('is_outview') == true)
                                            ->table([
                                                TableColumn::make(__('breakdown-form.attractions.sub_attraction'))->width('60%'),
                                                TableColumn::make(__('breakdown-form.attractions.price'))->width('40%'),
                                            ])
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('sub_attraction_id')
                                                            ->label(__('breakdown-form.attractions.sub_attraction'))
                                                            ->options(\App\Models\Base\SubAttraction::pluck('name', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->disabled()
                                                            ->dehydrated(),
                                                        TextInput::make('price')
                                                            ->label(__('breakdown-form.attractions.price'))
                                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                                            ->required()
                                                            ->placeholder(__('breakdown-form.placeholders.enter_price'))
                                                            ->rules(['required', 'numeric', 'min:0'])
                                                            ->validationMessages([
                                                                'required' => __('breakdown-form.validation.required_field.price'),
                                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                                'min' => __('breakdown-form.validation.min'),
                                                            ]),
                                                    ])
                                            ])->addable(false)->deletable(false)
                                            ->columnSpanFull(),
                                    ])->addable(false)->deletable(false),
                            ]),
                    ]),

                Wizard\Step::make(__('breakdown-form.steps.companions'))
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Section::make(__('breakdown-form.companions.budgets_section'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('companion_base_meal_budget')
                                            ->label(__('breakdown-form.companions.companion_meal_budget'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(50)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.companion_meal_budget'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                        TextInput::make('companion_base_accommodation_budget')
                                            ->label(__('breakdown-form.companions.companion_accommodation_budget'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->default(100)
                                            ->required()
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.companion_accommodation_budget'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                    ]),
                            ]),
                        Section::make(__('breakdown-form.companions.section_title'))
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
                                                    return __('breakdown-form.companions.cannot_delete_in_use', ['count' => $usageCount]);
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
                                        TableColumn::make(__('breakdown-form.companions.companion_type')),
                                        TableColumn::make(__('breakdown-form.companions.per_day_price')),
                                        TableColumn::make(__('breakdown-form.companions.half_day_price')),
                                        TableColumn::make(__('breakdown-form.companions.per_hour_price')),
                                    ])
                                    ->schema([
                                        Select::make('companion_type_id')
                                            ->label(__('breakdown-form.companions.companion_type'))
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
                                            ->label(__('breakdown-form.companions.per_day_price'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder(__('breakdown-form.placeholders.enter_price'))
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.price'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                        TextInput::make('half_day_price')
                                            ->label(__('breakdown-form.companions.half_day_price'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder(__('breakdown-form.placeholders.enter_price'))
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.price'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                        TextInput::make('per_hour_price')
                                            ->label(__('breakdown-form.companions.per_hour_price'))
                                            ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                            ->formatStateUsing(fn($state) => $state == 0 ? null : $state)
                                            ->dehydrateStateUsing(fn($state) => $state ?: 0)
                                            ->required()
                                            ->placeholder(__('breakdown-form.placeholders.enter_price'))
                                            ->rules(['required', 'numeric', 'min:0'])
                                            ->validationMessages([
                                                'required' => __('breakdown-form.validation.required_field.price'),
                                                'numeric' => __('breakdown-form.validation.numeric'),
                                                'min' => __('breakdown-form.validation.min'),
                                            ]),
                                    ])
                                    ->addActionLabel(__('common-fields.add'))
                                    ->collapsible(),
                            ]),
                    ]),

                Wizard\Step::make(__('breakdown-form.steps.expenses'))
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Repeater::make('expenses')
                            ->hiddenLabel()
                            ->reorderable(false)
                            ->table([
                                TableColumn::make(__('common-fields.description')),
                                TableColumn::make(__('breakdown-form.expenses.charge_mode')),
                                TableColumn::make(__('breakdown-form.expenses.price')),
                            ])
                            ->schema([
                                TextInput::make('description')
                                    ->label(__('common-fields.description'))
                                    ->required(),
                                Select::make('charge_mode')
                                    ->label(__('breakdown-form.expenses.charge_mode'))
                                    ->options(\App\Enums\ChargeModeEnum::getOptions())
                                    ->required(),
                                TextInput::make('price')
                                    ->label(__('breakdown-form.expenses.price'))
                                    ->prefix(fn($record) => $record?->breakdown?->currency?->symbol)
                                    ->required()
                                    ->rules(['required', 'numeric', 'min:0'])
                                    ->validationMessages([
                                        'required' => __('breakdown-form.validation.required_field.price'),
                                        'numeric' => __('breakdown-form.validation.numeric'),
                                        'min' => __('breakdown-form.validation.min'),
                                    ]),
                            ])
                            ->addActionLabel(__('common-fields.add'))
                            ->collapsible(),
                    ]),
                ])->columnSpanFull()->skippable()
            ]);
        }
    }
