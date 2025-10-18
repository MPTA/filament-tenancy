<?php

namespace App\Filament\Tenant\Resources\MealTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MealTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('tenant-meal-types.sections.meal_type_information.title'))
                    ->description(__('tenant-meal-types.sections.meal_type_information.description'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('tenant-meal-types.fields.meal_type_name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('tenant-meal-types.placeholders.name'))
                            ->helperText(__('tenant-meal-types.helpers.name'))
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
                                table: 'meal_types',
                                column: 'slug',
                                ignoreRecord: true,
                                modifyRuleUsing: function ($rule) {
                                    return $rule->where('tenant_id', tenant()->id);
                                }
                            )
                            ->placeholder(__('tenant-meal-types.placeholders.slug'))
                            ->helperText(__('tenant-meal-types.helpers.slug'))
                            ->rules(['regex:/^[a-z0-9-]+$/']),
                        
                        Select::make('meal_category_id')
                            ->label(__('tenant-meal-types.fields.meal_category'))
                            ->relationship('mealCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder(__('tenant-meal-types.placeholders.meal_category'))
                            ->helperText(__('tenant-meal-types.helpers.meal_category'))
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        
                        Textarea::make('description')
                            ->label(__('common-fields.description'))
                            ->maxLength(1000)
                            ->placeholder(__('tenant-meal-types.placeholders.description'))
                            ->helperText(__('tenant-meal-types.helpers.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-meal-types.sections.pricing_information.title'))
                    ->description(__('tenant-meal-types.sections.pricing_information.description'))
                    ->schema([
                        TextInput::make('price')
                            ->label(__('common-fields.price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-meal-types.placeholders.price'))
                            ->helperText(__('tenant-meal-types.helpers.price'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                    ])
                    ->columns(1)
            ]);
    }
}
