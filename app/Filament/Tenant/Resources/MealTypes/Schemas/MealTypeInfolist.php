<?php

namespace App\Filament\Tenant\Resources\MealTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MealTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Meal Type Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Meal Type Name')
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage('Slug copied')
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('mealCategory.name')
                            ->label('Meal Category')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(3),
                
                Section::make('Description & Pricing')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('No description provided')
                            ->markdown()
                            ->columnSpanFull(),
                        
                        TextEntry::make('price')
                            ->label('Price')
                            ->money('USD')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-currency-dollar'),
                    ])
                    ->columns(1),
                
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID')
                            ->badge()
                            ->color('gray'),
                        
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M j, Y g:i A')
                            ->placeholder('Not available')
                            ->icon('heroicon-o-calendar'),
                        
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('M j, Y g:i A')
                            ->placeholder('Not available')
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(3)
                    ->collapsible()
            ]);
    }
}
