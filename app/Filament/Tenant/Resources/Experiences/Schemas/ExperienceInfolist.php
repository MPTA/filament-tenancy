<?php

namespace App\Filament\Tenant\Resources\Experiences\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Experience Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Experience Name')
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage('Slug copied')
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('charge_mode')
                            ->label('Charge Mode')
                            ->badge()
                            ->color('success'),
                        
                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        
                        IconEntry::make('is_free_for_guide')
                            ->label('Free for Guide')
                            ->boolean()
                            ->trueIcon('heroicon-o-gift')
                            ->falseIcon('heroicon-o-currency-dollar')
                            ->trueColor('success')
                            ->falseColor('gray'),
                        
                        IconEntry::make('is_free_for_other_companions')
                            ->label('Free for Companions')
                            ->boolean()
                            ->trueIcon('heroicon-o-gift')
                            ->falseIcon('heroicon-o-currency-dollar')
                            ->trueColor('success')
                            ->falseColor('gray'),
                    ])
                    ->columns(2),
                
                Section::make('Description & Content')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('No description provided')
                            ->markdown()
                            ->columnSpanFull(),
                        
                        TextEntry::make('content')
                            ->label('Content')
                            ->placeholder('No content provided')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                
                Section::make('Pricing Information')
                    ->schema([
                        TextEntry::make('price')
                            ->label('Price')
                            ->money('USD')
                            ->placeholder('Free')
                            ->icon('heroicon-o-currency-dollar'),
                        
                        TextEntry::make('currency.name')
                            ->label('Currency')
                            ->badge()
                            ->color('warning')
                            ->placeholder('Not specified'),
                    ])
                    ->columns(2),
                
                Section::make('Location Information')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Address')
                            ->placeholder('No address provided')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull(),
                        
                        TextEntry::make('city.name')
                            ->label('City')
                            ->badge()
                            ->color('info')
                            ->placeholder('Not specified'),
                        
                        TextEntry::make('district.name')
                            ->label('District')
                            ->badge()
                            ->color('info')
                            ->placeholder('Not specified'),
                    ])
                    ->columns(2),
                
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID')
                            ->badge()
                            ->color('gray'),
                        
                        TextEntry::make('creator.name')
                            ->label('Created By')
                            ->placeholder('Unknown')
                            ->icon('heroicon-o-user'),
                        
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
                    ->columns(2)
                    ->collapsible()
            ]);
    }
}
