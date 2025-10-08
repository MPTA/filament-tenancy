<?php

namespace App\Filament\Base\Resources\MealCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MealCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Meal Category Details')
                    ->description('Basic information about this meal category')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Category Name')
                                    ->translateLabel()
                                    ->icon('heroicon-o-tag')
                                    ->color('primary')
                                    ->weight('bold'),
                                
                                TextEntry::make('slug')
                                    ->label('URL Slug')
                                    ->copyable()
                                    ->copyMessage('Slug copied!')
                                    ->icon('heroicon-o-link')
                                    ->color('gray')
                                    ->badge(),
                            ]),
                        
                        TextEntry::make('description')
                            ->label('Description')
                            ->translateLabel()
                            ->icon('heroicon-o-document-text')
                            ->default('No description provided')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Status & Metadata')
                    ->description('Category status and timestamps')
                    ->icon('heroicon-o-cog')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                IconEntry::make('is_active')
                                    ->label('Active Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->label(fn ($state) => $state ? 'Active' : 'Inactive'),
                                
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('M d, Y H:i')
                                    ->icon('heroicon-o-clock')
                                    ->color('success'),
                                
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('M d, Y H:i')
                                    ->icon('heroicon-o-arrow-path')
                                    ->color('warning'),
                            ]),
                    ]),
            ]);
    }
}
