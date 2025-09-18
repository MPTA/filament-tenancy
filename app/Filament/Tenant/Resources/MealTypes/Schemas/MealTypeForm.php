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
                Section::make('Meal Type Information')
                    ->description('Enter the meal type details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Meal Type Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Breakfast, Lunch, Dinner, Snack')
                            ->helperText('Full name of the meal type')
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
                            ->placeholder('e.g., breakfast, lunch, dinner, snack')
                            ->helperText('URL-friendly identifier (auto-generated from name)')
                            ->rules(['regex:/^[a-z0-9-]+$/']),
                        
                        Select::make('meal_category_id')
                            ->label('Meal Category')
                            ->relationship('mealCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a meal category')
                            ->helperText('Category this meal type belongs to')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(1000)
                            ->placeholder('Brief description of the meal type')
                            ->helperText('Short description for listings and previews')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Pricing Information')
                    ->description('Set pricing for this meal type')
                    ->schema([
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Price for this meal type')
                            ->rules(['min:0']),
                    ])
                    ->columns(1)
            ]);
    }
}
