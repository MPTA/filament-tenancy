<?php

namespace App\Filament\Tenant\Resources\VehicleTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Vehicle Type Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Vehicle Type Name')
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage('Slug copied')
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('vehicleCategory.name')
                            ->label('Vehicle Category')
                            ->badge()
                            ->color('success'),
                        
                        IconEntry::make('is_vip')
                            ->label('VIP Service')
                            ->boolean()
                            ->trueIcon('heroicon-o-star')
                            ->falseIcon('heroicon-o-star')
                            ->trueColor('warning')
                            ->falseColor('gray'),
                    ])
                    ->columns(2),
                
                Section::make('Capacity & Specifications')
                    ->schema([
                        TextEntry::make('capacity_from')
                            ->label('Min Capacity')
                            ->suffix(' passengers')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-users'),
                        
                        TextEntry::make('capacity_to')
                            ->label('Max Capacity')
                            ->suffix(' passengers')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-users'),
                        
                        TextEntry::make('max_hour_per_day')
                            ->label('Max Hours/Day')
                            ->suffix(' hours')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('max_hour_half_day')
                            ->label('Max Hours/Half Day')
                            ->suffix(' hours')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-clock'),
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
                        
                        TextEntry::make('extra_hour_price')
                            ->label('Extra Hour Price')
                            ->money('USD')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-currency-dollar'),
                        
                        TextEntry::make('airport_transfer_price')
                            ->label('Airport Transfer Price')
                            ->money('USD')
                            ->placeholder('Not specified')
                            ->icon('heroicon-o-currency-dollar'),
                    ])
                    ->columns(2),
                
                Section::make('Media & Description')
                    ->schema([
                        TextEntry::make('cover')
                            ->label('Cover Image')
                            ->url(fn ($record) => $record->cover)
                            ->openUrlInNewTab()
                            ->placeholder('No cover image')
                            ->icon('heroicon-o-photo'),
                        
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('No description provided')
                            ->markdown()
                            ->columnSpanFull(),
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
