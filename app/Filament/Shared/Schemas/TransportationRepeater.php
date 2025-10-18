<?php

namespace App\Filament\Shared\Schemas;

use App\Enums\TransportModeEnum;
use App\Models\Base\BorderPoint;
use App\Models\Base\City;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class TransportationRepeater
{
    public static function make(bool $useRelationship = true): Repeater
    {
        $repeater = Repeater::make('transportations');
        
        if ($useRelationship) {
            $repeater->relationship('transportations');
        }
        
        return $repeater->schema([
                // Row 1: Transport Mode
                Select::make('transport_mode')
                    ->label(__('transportation.transport_mode'))
                    ->options(TransportModeEnum::getOptions())
                    ->required()
                    ->reactive()
                    ->native(false)
                    ->placeholder(__('transportation.select_mode')),

                // Row 2: From City and To City in one line
                Grid::make(2)
                    ->schema([
                        // From City
                        Grid::make(1)
                            ->schema([
                                Checkbox::make('use_custom_from_city')
                                    ->label(__('transportation.use_custom_from_city'))
                                    ->helperText(__('transportation.use_custom_from_city_helper'))
                                    ->reactive()
                                    ->hidden()
                                    ->default(false)
                                    ->dehydrated(true)
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $set('from_city_id', null);
                                        } else {
                                            $set('custom_from_city', null);
                                        }
                                    }),

                                Select::make('from_city_id')
                                    ->label(__('transportation.from_city'))
                                    ->options(fn() => City::all()
                                        ->sortBy('name')
                                        ->mapWithKeys(fn($city) => [$city->id => $city->name])
                                        ->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->placeholder(__('transportation.select_city'))
                                    ->visible(fn($get) => !$get('use_custom_from_city'))
                                    ->required(fn($get) => !$get('use_custom_from_city')),

                                TextInput::make('custom_from_city')
                                    ->label(__('transportation.from_city'))
                                    ->placeholder(__('transportation.enter_city_name'))
                                    ->helperText(__('transportation.international_city_helper'))
                                    ->maxLength(255)
                                    ->visible(fn($get) => $get('use_custom_from_city'))
                                    ->required(fn($get) => $get('use_custom_from_city')),
                            ]),

                        // To City
                        Grid::make(1)
                            ->schema([
                                Checkbox::make('use_custom_to_city')
                                    ->label(__('transportation.use_custom_to_city'))
                                    ->helperText(__('transportation.use_custom_to_city_helper'))
                                    ->reactive()
                                    ->hidden()
                                    ->default(false)
                                    ->dehydrated(true)
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $set('to_city_id', null);
                                        } else {
                                            $set('custom_to_city', null);
                                        }
                                    }),

                                Select::make('to_city_id')
                                    ->label(__('transportation.to_city'))
                                    ->options(fn() => City::all()
                                        ->sortBy('name')
                                        ->mapWithKeys(fn($city) => [$city->id => $city->name])
                                        ->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->placeholder(__('transportation.select_city'))
                                    ->visible(fn($get) => !$get('use_custom_to_city'))
                                    ->required(fn($get) => !$get('use_custom_to_city')),

                                TextInput::make('custom_to_city')
                                    ->label(__('transportation.to_city'))
                                    ->placeholder(__('transportation.enter_city_name'))
                                    ->helperText(__('transportation.international_city_helper'))
                                    ->maxLength(255)
                                    ->visible(fn($get) => $get('use_custom_to_city'))
                                    ->required(fn($get) => $get('use_custom_to_city')),
                            ]),
                    ]),

                // Row 2: Departure and Arrival Date/Time
                Grid::make(2)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('departure_date')
                                    ->label(__('transportation.departure_date'))
                                    ->required()
                                    ->reactive()
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Set arrival_date to same as departure_date when departure_date is selected
                                        if ($state) {
                                            $set('arrival_date', $state);
                                        }
                                    }),

                                TimePicker::make('departure_time')
                                    ->label(__('transportation.departure_time'))
                                    ->seconds(false)
                                    ->minutesStep(5),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('arrival_date')
                                    ->label(__('transportation.arrival_date'))
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->minDate(fn ($get) => $get('departure_date') ?: null)
                                    ->maxDate(fn ($get) => $get('departure_date') 
                                        ? \Carbon\Carbon::parse($get('departure_date'))->addDay()->format('Y-m-d')
                                        : null)
                                    ->helperText(fn ($get) => $get('departure_date')
                                        ? __('transportation.arrival_date_helper_with_departure')
                                        : __('transportation.arrival_date_helper_no_departure'))
                                    ->disabled(fn ($get) => !$get('departure_date')),

                                TimePicker::make('arrival_time')
                                    ->label(__('transportation.arrival_time'))
                                    ->seconds(false)
                                    ->minutesStep(5),
                            ]),
                    ]),

                // Row 3: Transport Number
                TextInput::make('transport_number')
                    ->label(__('transportation.transport_number'))
                    ->helperText(__('transportation.transport_number_helper'))
                    ->maxLength(100)
                    ->placeholder(__('transportation.transport_number_placeholder')),

                // Conditional Fields for AIR
                Grid::make(2)
                    ->schema([
                        TextInput::make('departure_airport_terminal')
                            ->label(__('transportation.departure_terminal'))
                            ->maxLength(50)
                            ->placeholder(__('transportation.terminal_departure_placeholder')),

                        TextInput::make('arrival_airport_terminal')
                            ->label(__('transportation.arrival_terminal'))
                            ->maxLength(50)
                            ->placeholder(__('transportation.terminal_arrival_placeholder')),
                    ])
                    ->visible(fn($get) => $get('transport_mode') === TransportModeEnum::AIR->value),

                // Conditional Fields for LAND
                Grid::make(2)
                    ->schema([
                        Select::make('entry_border_id')
                            ->label(__('transportation.entry_border'))
                            ->options(fn() => BorderPoint::all()
                                ->sortBy('name')
                                ->mapWithKeys(fn($border) => [$border->id => $border->name])
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->placeholder(__('transportation.select_border_point')),

                        Select::make('exit_border_id')
                            ->label(__('transportation.exit_border'))
                            ->options(fn() => BorderPoint::all()
                                ->sortBy('name')
                                ->mapWithKeys(fn($border) => [$border->id => $border->name])
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->placeholder(__('transportation.select_border_point')),
                    ])
                    ->visible(fn($get) => $get('transport_mode') === TransportModeEnum::LAND->value),

                // Note: TRAIN mode has no additional fields (train stations will be added in phase 2)
            ])
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->collapsible(false)
            ->defaultItems(2)
            ->minItems(2)
            ->maxItems(2)
            ->itemLabel(function (array $state, $component) {
                static $labelCounter = [];
                static $requestId = null;
                
                // Reset counter for new requests
                $currentRequestId = spl_object_id($component->getLivewire());
                if ($requestId !== $currentRequestId) {
                    $labelCounter = [];
                    $requestId = $currentRequestId;
                }
                
                // Create a unique key for this item
                $itemKey = json_encode($state);
                
                // If we haven't seen this item, assign it the next index
                if (!isset($labelCounter[$itemKey])) {
                    $labelCounter[$itemKey] = count($labelCounter);
                }
                
                $index = $labelCounter[$itemKey];
                
                // Return label based on index
                if ($index === 0) {
                    return __('transportation.entry_transportation');
                } elseif ($index === 1) {
                    return __('transportation.exit_transportation');
                }
                
                return __('transportation.transportation_item', ['number' => ($index + 1)]);
            })
            ->helperText(__('transportation.repeater_helper'));
    }
}

