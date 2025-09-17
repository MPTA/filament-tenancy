<?php

namespace App\Filament\Base\Resources\Currencies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CurrencyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Currency Details')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Currency Code')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage('Currency code copied')
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('name')
                            ->label('Currency Name')
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('symbol')
                            ->label('Currency Symbol')
                            ->badge()
                            ->color('success')
                            ->placeholder('No symbol set'),
                    ])
                    ->columns(3),
                
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
