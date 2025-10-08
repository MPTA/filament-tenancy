<?php

namespace App\Filament\Base\Resources\MealCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MealCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Meal Category Information')
                    ->description('Enter the basic information for this meal category')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Category Name')
                                    ->required()
                                    ->translateLabel()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Breakfast, Lunch, Dinner')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Auto-generate slug if it's empty
                                        if (empty($get('slug')) && is_string($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->columnSpan(1),
                                
                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., breakfast')
                                    ->helperText('Auto-generated from name, or enter custom slug')
                                    ->alphaDash()
                                    ->rules(['alpha_dash'])
                                    ->columnSpan(1),
                            ]),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->translateLabel()
                            ->rows(3)
                            ->placeholder('Enter a brief description of this meal category')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Settings')
                    ->description('Configure category settings')
                    ->icon('heroicon-o-cog')
                    ->collapsible()
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->helperText('Enable or disable this meal category')
                            ->default(true)
                            ->inline(false)
                            ->required(),
                    ]),
            ]);
    }
}
