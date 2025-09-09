<?php

namespace App\Filament\Base\Resources\MealCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MealCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->translateLabel()
                    ->afterStateUpdated(function ($state, $set) {
                        // Convert string to array format for spatie/laravel-translatable
                        if (is_string($state)) {
                            $set('name', [app()->getLocale() => $state]);
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description')
                    ->translateLabel()
                    ->afterStateUpdated(function ($state, $set) {
                        // Convert string to array format for spatie/laravel-translatable
                        if (is_string($state)) {
                            $set('description', [app()->getLocale() => $state]);
                        }
                    }),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
