<?php

namespace App\Filament\Tenant\Resources\CompanionTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanionTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Companion Type Name')
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage('Slug copied')
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('companionCategory.name')
                            ->label('Companion Category')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(3),
                
                Section::make('Language Requirements')
                    ->schema([
                        TextEntry::make('nativeLanguage.name')
                            ->label('Native Language')
                            ->badge()
                            ->color('info')
                            ->placeholder('Not specified'),
                        
                        TextEntry::make('speakingLanguage.name')
                            ->label('Speaking Language')
                            ->badge()
                            ->color('info')
                            ->placeholder('Not specified'),
                    ])
                    ->columns(2),
                
                Section::make('Pricing Information')
                    ->schema([
                        TextEntry::make('per_day_price')
                            ->label('Per Day Price')
                            ->money('USD')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-currency-dollar'),
                        
                        TextEntry::make('half_day_price')
                            ->label('Half Day Price')
                            ->money('USD')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-currency-dollar'),
                        
                        TextEntry::make('per_hour_price')
                            ->label('Per Hour Price')
                            ->money('USD')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('extra_hour_price')
                            ->label('Extra Hour Price')
                            ->money('USD')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-plus-circle'),
                    ])
                    ->columns(2),
                
                Section::make('Service Limits')
                    ->schema([
                        TextEntry::make('max_hour_per_day')
                            ->label('Max Hours Per Day')
                            ->suffix(' hours')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-sun'),
                        
                        TextEntry::make('max_hour_half_day')
                            ->label('Max Hours Half Day')
                            ->suffix(' hours')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-moon'),
                    ])
                    ->columns(2),
                
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
