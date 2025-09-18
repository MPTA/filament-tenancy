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
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->placeholder('1')
                            ->helperText('Minimum number of passengers')
                            ->suffix('passengers'),
                        
                        TextInput::make('capacity_to')
                            ->label('Maximum Capacity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->placeholder('4')
                            ->helperText('Maximum number of passengers')
                            ->suffix('passengers')
                            ->rules(['gte:capacity_from']),
                        
                        TextInput::make('max_hour_per_day')
                            ->label('Max Hours Per Day')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(24)
                            ->placeholder('8')
                            ->helperText('Maximum hours allowed per day')
                            ->suffix('hours'),
                        
                        TextInput::make('max_hour_half_day')
                            ->label('Max Hours Half Day')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->placeholder('4')
                            ->helperText('Maximum hours for half day service')
                            ->suffix('hours'),
                    ])
                    ->columns(2),
                
                Section::make('Pricing Configuration')
                    ->description('Set pricing for different service types')
                    ->schema([
                        TextInput::make('per_day_price')
                            ->label('Per Day Price')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->helperText('Price for full day service')
                            ->prefix('$'),
                        
                        TextInput::make('half_day_price')
                            ->label('Half Day Price')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->helperText('Price for half day service')
                            ->prefix('$'),
                        
                        TextInput::make('extra_hour_price')
                            ->label('Extra Hour Price')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->helperText('Price for each extra hour')
                            ->prefix('$'),
                        
                        TextInput::make('airport_transfer_price')
                            ->label('Airport Transfer Price')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->helperText('Price for airport transfer service')
                            ->prefix('$'),
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
