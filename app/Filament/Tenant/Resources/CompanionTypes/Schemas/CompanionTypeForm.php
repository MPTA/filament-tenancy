<?php

namespace App\Filament\Tenant\Resources\CompanionTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CompanionTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Enter the basic companion type details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Companion Type Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Professional Guide, Cultural Companion')
                            ->helperText('Full name of the companion type')
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
                            ->placeholder('e.g., professional-guide, cultural-companion')
                            ->helperText('URL-friendly identifier (auto-generated from name)')
                            ->rules(['regex:/^[a-z0-9-]+$/']),
                        
                        Select::make('companion_category_id')
                            ->label('Companion Category')
                            ->relationship('companionCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a companion category')
                            ->helperText('Category this companion type belongs to')
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $query = \App\Models\Tenants\CompanionType::where('tenant_id', tenant('id'))
                                            ->where('companion_category_id', $value)
                                            ->where('native_language_id', request()->input('native_language_id'))
                                            ->where('speaking_language_id', request()->input('speaking_language_id'));
                                        
                                        if (request()->route('record')) {
                                            $query->where('id', '!=', request()->route('record'));
                                        }
                                        
                                        if ($query->exists()) {
                                            $fail('A companion type with this category, native language, and speaking language combination already exists for this tenant. Please choose different values.');
                                        }
                                    };
                                },
                            ]),
                    ])
                    ->columns(2),
                
                Section::make('Language Requirements')
                    ->description('Specify language requirements for this companion type')
                    ->schema([
                        Select::make('native_language_id')
                            ->label('Native Language')
                            ->relationship('nativeLanguage', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select native language')
                            ->helperText('Primary language of the companion')
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $query = \App\Models\Tenants\CompanionType::where('tenant_id', tenant('id'))
                                            ->where('companion_category_id', request()->input('companion_category_id'))
                                            ->where('native_language_id', $value)
                                            ->where('speaking_language_id', request()->input('speaking_language_id'));
                                        
                                        if (request()->route('record')) {
                                            $query->where('id', '!=', request()->route('record'));
                                        }
                                        
                                        if ($query->exists()) {
                                            $fail('A companion type with this category, native language, and speaking language combination already exists for this tenant. Please choose different values.');
                                        }
                                    };
                                },
                            ]),
                        
                        Select::make('speaking_language_id')
                            ->label('Speaking Language')
                            ->relationship('speakingLanguage', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select speaking language')
                            ->helperText('Language the companion can speak')
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $query = \App\Models\Tenants\CompanionType::where('tenant_id', tenant('id'))
                                            ->where('companion_category_id', request()->input('companion_category_id'))
                                            ->where('native_language_id', request()->input('native_language_id'))
                                            ->where('speaking_language_id', $value);
                                        
                                        if (request()->route('record')) {
                                            $query->where('id', '!=', request()->route('record'));
                                        }
                                        
                                        if ($query->exists()) {
                                            $fail('A companion type with this category, native language, and speaking language combination already exists for this tenant. Please choose different values.');
                                        }
                                    };
                                },
                            ]),
                    ])
                    ->columns(2),
                
                Section::make('Pricing Configuration')
                    ->description('Set pricing for different service durations')
                    ->schema([
                        TextInput::make('per_day_price')
                            ->label('Per Day Price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Price for full day service'),
                        
                        TextInput::make('half_day_price')
                            ->label('Half Day Price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Price for half day service'),
                        
                        TextInput::make('per_hour_price')
                            ->label('Per Hour Price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Price per hour of service'),
                        
                        TextInput::make('extra_hour_price')
                            ->label('Extra Hour Price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Price for additional hours beyond limit'),
                    ])
                    ->columns(2),
                
                Section::make('Service Limits')
                    ->description('Define maximum service hours and time limits')
                    ->schema([
                        TextInput::make('max_hour_per_day')
                            ->label('Max Hours Per Day')
                            ->numeric()
                            ->suffix('hours')
                            ->placeholder('8')
                            ->helperText('Maximum hours allowed per day'),
                        
                        TextInput::make('max_hour_half_day')
                            ->label('Max Hours Half Day')
                            ->numeric()
                            ->suffix('hours')
                            ->placeholder('4')
                            ->helperText('Maximum hours allowed for half day service'),
                    ])
                    ->columns(2),
                
                Section::make('Budget Configuration')
                    ->description('Set base budgets for meals and accommodation')
                    ->schema([
                        TextInput::make('base_meal_budget')
                            ->label('Base Meal Budget')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Base budget for meals per day'),
                        
                        TextInput::make('base_accommodation_budget')
                            ->label('Base Accommodation Budget')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Base budget for accommodation per day'),
                        
                        Select::make('currency_id')
                            ->label('Currency')
                            ->relationship('currency', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select currency')
                            ->helperText('Currency for all pricing'),
                    ])
                    ->columns(3)
            ]);
    }
}
