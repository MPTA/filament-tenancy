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
                Section::make(__('tenant-vehicle-types.sections.basic_information.title'))
                    ->description(__('tenant-vehicle-types.sections.basic_information.description'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('tenant-vehicle-types.fields.vehicle_type_name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('tenant-vehicle-types.placeholders.name'))
                            ->helperText(__('tenant-vehicle-types.helpers.name'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        
                        TextInput::make('slug')
                            ->label(__('common-fields.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: 'vehicle_types',
                                column: 'slug',
                                ignoreRecord: true,
                                modifyRuleUsing: function ($rule) {
                                    return $rule->where('tenant_id', tenant()->id);
                                }
                            )
                            ->placeholder(__('tenant-vehicle-types.placeholders.slug'))
                            ->helperText(__('tenant-vehicle-types.helpers.slug'))
                            ->rules(['regex:/^[a-z0-9-]+$/']),
                        
                        Select::make('vehicle_category_id')
                            ->label(__('tenant-vehicle-types.fields.vehicle_category'))
                            ->relationship('vehicleCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder(__('tenant-vehicle-types.placeholders.vehicle_category'))
                            ->helperText(__('tenant-vehicle-types.helpers.vehicle_category'))
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        
                        TextInput::make('cover')
                            ->label(__('tenant-vehicle-types.fields.cover_image_url'))
                            ->url()
                            ->placeholder(__('tenant-vehicle-types.placeholders.cover_url'))
                            ->helperText(__('tenant-vehicle-types.helpers.cover_url')),
                        
                        Textarea::make('description')
                            ->label(__('common-fields.description'))
                            ->maxLength(1000)
                            ->placeholder(__('tenant-vehicle-types.placeholders.description'))
                            ->helperText(__('tenant-vehicle-types.helpers.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-vehicle-types.sections.capacity_specifications.title'))
                    ->description(__('tenant-vehicle-types.sections.capacity_specifications.description'))
                    ->schema([
                        TextInput::make('capacity_from')
                            ->label(__('tenant-vehicle-types.fields.minimum_capacity'))
                            ->required()
                            ->suffix(__('tenant-vehicle-types.suffixes.passengers'))
                            ->placeholder(__('tenant-vehicle-types.placeholders.capacity_min'))
                            ->helperText(__('tenant-vehicle-types.helpers.capacity_min'))
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
                            ->label(__('tenant-vehicle-types.fields.maximum_capacity'))
                            ->required()
                            ->suffix(__('tenant-vehicle-types.suffixes.passengers'))
                            ->placeholder(__('tenant-vehicle-types.placeholders.capacity_max'))
                            ->helperText(__('tenant-vehicle-types.helpers.capacity_max'))
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
                            ->label(__('tenant-vehicle-types.fields.max_hours_per_day'))
                            ->suffix(__('tenant-vehicle-types.suffixes.hours'))
                            ->placeholder(__('tenant-vehicle-types.placeholders.max_hours_day'))
                            ->helperText(__('tenant-vehicle-types.helpers.max_hours_day'))
                            ->rules(['nullable', 'integer', 'min:1', 'max:24'])
                            ->inputMode('numeric'),
                        
                        TextInput::make('max_hour_half_day')
                            ->label(__('tenant-vehicle-types.fields.max_hours_half_day'))
                            ->suffix(__('tenant-vehicle-types.suffixes.hours'))
                            ->placeholder(__('tenant-vehicle-types.placeholders.max_hours_half'))
                            ->helperText(__('tenant-vehicle-types.helpers.max_hours_half'))
                            ->rules(['nullable', 'integer', 'min:1', 'max:12'])
                            ->inputMode('numeric'),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-vehicle-types.sections.pricing_configuration.title'))
                    ->description(__('tenant-vehicle-types.sections.pricing_configuration.description'))
                    ->schema([
                        TextInput::make('per_day_price')
                            ->label(__('tenant-vehicle-types.fields.per_day_price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-vehicle-types.placeholders.price'))
                            ->helperText(__('tenant-vehicle-types.helpers.per_day_price'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                        
                        TextInput::make('half_day_price')
                            ->label(__('tenant-vehicle-types.fields.half_day_price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-vehicle-types.placeholders.price'))
                            ->helperText(__('tenant-vehicle-types.helpers.half_day_price'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                        
                        TextInput::make('extra_hour_price')
                            ->label(__('tenant-vehicle-types.fields.extra_hour_price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-vehicle-types.placeholders.price'))
                            ->helperText(__('tenant-vehicle-types.helpers.extra_hour_price'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                        
                        TextInput::make('airport_transfer_price')
                            ->label(__('tenant-vehicle-types.fields.airport_transfer_price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-vehicle-types.placeholders.price'))
                            ->helperText(__('tenant-vehicle-types.helpers.airport_transfer_price'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-vehicle-types.sections.service_options.title'))
                    ->description(__('tenant-vehicle-types.sections.service_options.description'))
                    ->schema([
                        Toggle::make('is_vip')
                            ->label(__('tenant-vehicle-types.fields.vip_service'))
                            ->helperText(__('tenant-vehicle-types.helpers.vip_service'))
                            ->default(false),
                    ])
                    ->columns(1)
            ]);
    }
}
