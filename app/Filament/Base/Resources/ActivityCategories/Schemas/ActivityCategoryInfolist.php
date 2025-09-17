<?php

namespace App\Filament\Base\Resources\ActivityCategories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Category Name')
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage('Slug copied')
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('type')
                            ->label('Category Type')
                            ->badge()
                            ->color('success'),
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
