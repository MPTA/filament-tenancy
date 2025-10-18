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
                Section::make(__('tenant-companion-types.sections.basic_information.title'))
                    ->description(__('tenant-companion-types.sections.basic_information.description'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('tenant-companion-types.fields.companion_type_name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('tenant-companion-types.placeholders.name'))
                            ->helperText(__('tenant-companion-types.helpers.name'))
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
                                table: 'companion_types',
                                column: 'slug',
                                ignoreRecord: true,
                                modifyRuleUsing: function ($rule) {
                                    return $rule->where('tenant_id', tenant()->id);
                                }
                            )
                            ->placeholder(__('tenant-companion-types.placeholders.slug'))
                            ->helperText(__('tenant-companion-types.helpers.slug'))
                            ->rules(['regex:/^[a-z0-9-]+$/']),
                        
                        Select::make('companion_category_id')
                            ->label(__('common-fields.companion_category'))
                            ->relationship('companionCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder(__('tenant-companion-types.placeholders.companion_category'))
                            ->helperText(__('tenant-companion-types.helpers.category'))
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
                                            $fail(__('tenant-companion-types.validations.duplicate_combination'));
                                        }
                                    };
                                },
                            ]),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-companion-types.sections.language_requirements.title'))
                    ->description(__('tenant-companion-types.sections.language_requirements.description'))
                    ->schema([
                        Select::make('native_language_id')
                            ->label(__('common-fields.native_language'))
                            ->relationship('nativeLanguage', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder(__('tenant-companion-types.placeholders.native_language'))
                            ->helperText(__('tenant-companion-types.helpers.native_language'))
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
                                            $fail(__('tenant-companion-types.validations.duplicate_combination'));
                                        }
                                    };
                                },
                            ]),
                        
                        Select::make('speaking_language_id')
                            ->label(__('common-fields.speaking_language'))
                            ->relationship('speakingLanguage', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder(__('tenant-companion-types.placeholders.speaking_language'))
                            ->helperText(__('tenant-companion-types.helpers.speaking_language'))
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
                                            $fail(__('tenant-companion-types.validations.duplicate_combination'));
                                        }
                                    };
                                },
                            ]),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-companion-types.sections.pricing_configuration.title'))
                    ->description(__('tenant-companion-types.sections.pricing_configuration.description'))
                    ->schema([
                        TextInput::make('per_day_price')
                            ->label(__('common-fields.per_day_price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-companion-types.placeholders.price_placeholder'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal')
                            ->helperText(__('tenant-companion-types.helpers.per_day_price')),
                        
                        TextInput::make('half_day_price')
                            ->label(__('common-fields.half_day_price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-companion-types.placeholders.price_placeholder'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal')
                            ->helperText(__('tenant-companion-types.helpers.half_day_price')),
                        
                        TextInput::make('per_hour_price')
                            ->label(__('common-fields.per_hour_price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-companion-types.placeholders.price_placeholder'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal')
                            ->helperText(__('tenant-companion-types.helpers.per_hour_price')),
                        
                        TextInput::make('extra_hour_price')
                            ->label(__('common-fields.extra_hour_price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-companion-types.placeholders.price_placeholder'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal')
                            ->helperText(__('tenant-companion-types.helpers.extra_hour_price')),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-companion-types.sections.service_limits.title'))
                    ->description(__('tenant-companion-types.sections.service_limits.description'))
                    ->schema([
                        TextInput::make('max_hour_per_day')
                            ->label(__('common-fields.max_hours_per_day'))
                            ->suffix(__('tenant-companion-types.suffixes.hours'))
                            ->placeholder(__('tenant-companion-types.placeholders.max_hours_day'))
                            ->helperText(__('tenant-companion-types.helpers.max_hours_per_day'))
                            ->rules(['nullable', 'integer', 'min:1'])
                            ->inputMode('numeric'),
                        
                        TextInput::make('max_hour_half_day')
                            ->label(__('common-fields.max_hours_half_day'))
                            ->suffix(__('tenant-companion-types.suffixes.hours'))
                            ->placeholder(__('tenant-companion-types.placeholders.max_hours_half'))
                            ->helperText(__('tenant-companion-types.helpers.max_hours_half_day'))
                            ->rules(['nullable', 'integer', 'min:1'])
                            ->inputMode('numeric'),
                    ])
                    ->columns(2),
            ]);
    }
}
