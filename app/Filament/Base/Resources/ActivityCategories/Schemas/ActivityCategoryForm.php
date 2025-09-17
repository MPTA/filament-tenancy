<?php

namespace App\Filament\Base\Resources\ActivityCategories\Schemas;

use App\Enums\ActivityCategoryTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ActivityCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activity Category Information')
                    ->description('Enter the activity category details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Category Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Adventure Sports, Cultural Activities')
                            ->helperText('Full name of the activity category')
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
                            ->placeholder('e.g., adventure-sports, cultural-activities')
                            ->helperText('URL-friendly identifier (auto-generated from name)')
                            ->rules(['regex:/^[a-z0-9-]+$/']),
                        
                        Select::make('type')
                            ->label('Category Type')
                            ->options(ActivityCategoryTypeEnum::class)
                            ->required()
                            ->placeholder('Select a category type')
                            ->helperText('Type of activity category')
                            ->searchable(),
                    ])
                    ->columns(2)
            ]);
    }
}
