<?php

namespace App\Filament\Shared\Schemas;

use App\Enums\TransportModeEnum;
use App\Models\Base\BorderPoint;
use App\Models\Base\City;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
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
                // Row 1: Transport Mode, From City, To City
                Grid::make(3)
                            ->schema([
                                Select::make('transport_mode')
                                    ->label('Transport Mode')
                                    ->options(TransportModeEnum::getOptions())
                                    ->required()
                                    ->reactive()
                                    ->native(false)
                                    ->placeholder('Select mode'),

                                Select::make('from_city_id')
                                    ->label('From City')
                                    ->options(fn() => City::all()
                                        ->sortBy('name')
                                        ->mapWithKeys(fn($city) => [$city->id => $city->name])
                                        ->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select city'),

                                Select::make('to_city_id')
                                    ->label('To City')
                                    ->options(fn() => City::all()
                                        ->sortBy('name')
                                        ->mapWithKeys(fn($city) => [$city->id => $city->name])
                                        ->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select city'),
                    ]),

                // Row 2: Departure and Arrival Date/Time
                Grid::make(2)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('departure_date')
                                    ->label('Departure Date')
                                    ->required()
                                    ->reactive()
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Clear arrival_date if it's not within valid range
                                        $arrivalDate = $get('arrival_date');
                                        if ($state && $arrivalDate) {
                                            $departure = \Carbon\Carbon::parse($state);
                                            $arrival = \Carbon\Carbon::parse($arrivalDate);
                                            
                                            // If arrival is before departure or more than 1 day after
                                            if ($arrival->lt($departure) || $arrival->gt($departure->copy()->addDay())) {
                                                $set('arrival_date', null);
                                            }
                                        }
                                    }),

                                TimePicker::make('departure_time')
                                    ->label('Departure Time')
                                    ->seconds(false)
                                    ->minutesStep(5),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('arrival_date')
                                    ->label('Arrival Date')
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->minDate(fn ($get) => $get('departure_date') ?: null)
                                    ->maxDate(fn ($get) => $get('departure_date') 
                                        ? \Carbon\Carbon::parse($get('departure_date'))->addDay()->format('Y-m-d')
                                        : null)
                                    ->helperText(fn ($get) => $get('departure_date')
                                        ? 'Must be same day or maximum 1 day after departure'
                                        : 'Select departure date first')
                                    ->disabled(fn ($get) => !$get('departure_date')),

                                TimePicker::make('arrival_time')
                                    ->label('Arrival Time')
                                    ->seconds(false)
                                    ->minutesStep(5),
                            ]),
                    ]),

                // Row 3: Transport Number
                TextInput::make('transport_number')
                    ->label('Transport Number')
                    ->helperText('Flight number, train number, or bus number')
                    ->maxLength(100)
                    ->placeholder('e.g., IRA123, TR456'),

                // Conditional Fields for AIR
                Grid::make(2)
                    ->schema([
                        TextInput::make('departure_airport_terminal')
                            ->label('Departure Terminal')
                            ->maxLength(50)
                            ->placeholder('e.g., Terminal 2'),

                        TextInput::make('arrival_airport_terminal')
                            ->label('Arrival Terminal')
                            ->maxLength(50)
                            ->placeholder('e.g., Terminal 1'),
                    ])
                    ->visible(fn($get) => $get('transport_mode') === TransportModeEnum::AIR->value),

                // Conditional Fields for LAND
                Grid::make(2)
                    ->schema([
                        Select::make('entry_border_id')
                            ->label('Entry Border')
                            ->options(fn() => BorderPoint::all()
                                ->sortBy('name')
                                ->mapWithKeys(fn($border) => [$border->id => $border->name])
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->placeholder('Select border point'),

                        Select::make('exit_border_id')
                            ->label('Exit Border')
                            ->options(fn() => BorderPoint::all()
                                ->sortBy('name')
                                ->mapWithKeys(fn($border) => [$border->id => $border->name])
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->placeholder('Select border point'),
                    ])
                    ->visible(fn($get) => $get('transport_mode') === TransportModeEnum::LAND->value),

                // Note: TRAIN mode has no additional fields (train stations will be added in phase 2)
            ])
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->collapsible(false)
            ->itemLabel(fn (?array $state = null, ?int $index = null): string => 
                $index === 0 ? '✈️ Entry Transportation' : '✈️ Exit Transportation'
            )
            ->helperText('Entry and exit transportation for the group');
    }
}

