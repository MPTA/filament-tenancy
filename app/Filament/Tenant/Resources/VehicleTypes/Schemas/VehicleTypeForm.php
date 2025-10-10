<?php

namespace App\Filament\Tenant\Resources\VehicleTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VehicleTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Enter the vehicle type details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Vehicle Type Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Sedan, SUV, Minivan, Bus')
                            ->helperText('Full name of the vehicle type')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., sedan, suv, minivan, bus')
                            ->helperText('URL-friendly identifier (auto-generated from name)')
                            ->rules(['regex:/^[a-z0-9-]+$/']),
                        
                        Select::make('vehicle_category_id')
                            ->label('Vehicle Category')
                            ->relationship('vehicleCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a vehicle category')
                            ->helperText('Category this vehicle type belongs to')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        
                        TextInput::make('cover')
                            ->label('Cover Image URL')
                            ->url()
                            ->placeholder('https://example.com/image.jpg')
                            ->helperText('URL to the vehicle cover image'),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(1000)
                            ->placeholder('Brief description of the vehicle type')
                            ->helperText('Short description for listings and previews')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Capacity & Specifications')
                    ->description('Set passenger capacity and specifications')
                    ->schema([
                        TextInput::make('capacity_from')
                            ->label('Minimum Capacity')
                            ->required()
                            ->suffix('passengers')
                            ->placeholder('1')
                            ->helperText('Minimum number of passengers')
                            ->rules(['required', 'integer', 'min:1', 'max:100'])
                            ->inputMode('numeric')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $get, $set) {
                                $capacityTo = $get('capacity_to');
                                if ($state && $capacityTo && $capacityTo < $state) {
                                    $set('capacity_to', $state);
                                }
                            }),
                        
                        TextInput::make('capacity_to')
                            ->label('Maximum Capacity')
                            ->required()
                            ->suffix('passengers')
                            ->placeholder('4')
                            ->helperText('Maximum number of passengers')
                            ->rules(['required', 'integer', 'min:1', 'max:100'])
                            ->inputMode('numeric')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $get, $set) {
                                $capacityFrom = $get('capacity_from');
                                if ($state && $capacityFrom && $state < $capacityFrom) {
                                    $set('capacity_to', $capacityFrom);
                                }
                            }),
                        
                        TextInput::make('max_hour_per_day')
                            ->label('Max Hours Per Day')
                            ->suffix('hours')
                            ->placeholder('8')
                            ->helperText('Maximum hours allowed per day')
                            ->rules(['nullable', 'integer', 'min:1', 'max:24'])
                            ->inputMode('numeric'),
                        
                        TextInput::make('max_hour_half_day')
                            ->label('Max Hours Half Day')
                            ->suffix('hours')
                            ->placeholder('4')
                            ->helperText('Maximum hours for half day service')
                            ->rules(['nullable', 'integer', 'min:1', 'max:12'])
                            ->inputMode('numeric'),
                    ])
                    ->columns(2),
                
                Section::make('Pricing Configuration')
                    ->description('Set pricing for different service types')
                    ->schema([
                        TextInput::make('per_day_price')
                            ->label('Per Day Price')
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder('0.00')
                            ->helperText('Price for full day service')
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                        
                        TextInput::make('half_day_price')
                            ->label('Half Day Price')
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder('0.00')
                            ->helperText('Price for half day service')
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                        
                        TextInput::make('extra_hour_price')
                            ->label('Extra Hour Price')
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder('0.00')
                            ->helperText('Price for each extra hour')
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                        
                        TextInput::make('airport_transfer_price')
                            ->label('Airport Transfer Price')
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder('0.00')
                            ->helperText('Price for airport transfer service')
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                    ])
                    ->columns(2),
                
                Section::make('Service Options')
                    ->description('Configure service options and features')
                    ->schema([
                        Toggle::make('is_vip')
                            ->label('VIP Service')
                            ->helperText('Enable VIP service features')
                            ->default(false),
                    ])
                    ->columns(1)
            ]);
    }
}
