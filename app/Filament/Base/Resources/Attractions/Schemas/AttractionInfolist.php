<?php

namespace App\Filament\Base\Resources\Attractions\Schemas;

use App\Enums\AttractionTypeEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttractionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextEntry::make('name')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('description')
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                        TextEntry::make('type')
                            ->badge()
                            ->color(fn ($state): string => match ($state?->value ?? $state) {
                                'natural' => 'success',
                                'man_made' => 'info',
                                'cultural' => 'warning',
                                'sport' => 'danger',
                                'events' => 'primary',
                                'leisure' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state): string => $state instanceof AttractionTypeEnum ? $state->label() : AttractionTypeEnum::from($state)->label()),
                        IconEntry::make('is_active')
                            ->boolean()
                            ->label('Status'),
                    ])
                    ->columns(2),

                Section::make('Location Information')
                    ->schema([
                        TextEntry::make('country.name')
                            ->label('Country'),
                        TextEntry::make('city.name')
                            ->label('City'),
                        TextEntry::make('district.name')
                            ->label('District')
                            ->placeholder('Not specified'),
                        TextEntry::make('address')
                            ->placeholder('No address provided')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Coordinates & Rating')
                    ->schema([
                        TextEntry::make('latitude')
                            ->numeric()
                            ->placeholder('Not specified')
                            ->suffix('°'),
                        TextEntry::make('longitude')
                            ->numeric()
                            ->placeholder('Not specified')
                            ->suffix('°'),
                        TextEntry::make('rating')
                            ->numeric()
                            ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '/5' : 'Not rated')
                            ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'gray')),
                    ])
                    ->columns(3),

                Section::make('Pricing Information')
                    ->schema([
                        TextEntry::make('local_price')
                            ->money(fn ($record) => $record->country?->currency?->code ?? 'USD')
                            ->placeholder('Free'),
                        TextEntry::make('foreigner_price')
                            ->money(fn ($record) => $record->country?->currency?->code ?? 'USD')
                            ->placeholder('Free'),
                    ])
                    ->columns(2),

                Section::make('Visit Information')
                    ->schema([
                        TextEntry::make('opening_hours')
                            ->placeholder('Not specified')
                            ->columnSpanFull(),
                        TextEntry::make('suggested_duration')
                            ->placeholder('Not specified')
                            ->columnSpanFull(),
                        TextEntry::make('duration_hours')
                            ->formatStateUsing(fn ($state) => $state ? ($state['min'] ?? '-') . ' - ' . ($state['max'] ?? '-') . ' hours' : 'Not specified')
                            ->label('Duration Range'),
                        TextEntry::make('suggested_season')
                            ->placeholder('Not specified')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Seasonal Availability')
                    ->schema([
                        IconEntry::make('season_spring')
                            ->boolean()
                            ->label('Spring'),
                        IconEntry::make('season_summer')
                            ->boolean()
                            ->label('Summer'),
                        IconEntry::make('season_autumn')
                            ->boolean()
                            ->label('Autumn'),
                        IconEntry::make('season_winter')
                            ->boolean()
                            ->label('Winter'),
                    ])
                    ->columns(4)
                    ->collapsible(),

                Section::make('Ticket & Tips Information')
                    ->schema([
                        TextEntry::make('ticket_info')
                            ->placeholder('Not specified')
                            ->columnSpanFull(),
                        TextEntry::make('tips')
                            ->placeholder('Not specified')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('External Links')
                    ->schema([
                        TextEntry::make('link')
                            ->placeholder('Not specified')
                            ->url(fn ($record) => $record->link ?? '', shouldOpenInNewTab: true)
                            ->copyable()
                            ->columnSpanFull(),
                        TextEntry::make('image_url')
                            ->placeholder('Not specified')
                            ->url(fn ($record) => $record->image_url ?? '', shouldOpenInNewTab: true)
                            ->copyable()
                            ->columnSpanFull(),
                        ImageEntry::make('image_url')
                            ->label('Preview')
                            ->size(200)
                            ->visible(fn ($record) => !empty($record->image_url)),
                        TextEntry::make('external_id')
                            ->label('External ID')
                            ->placeholder('Not specified')
                            ->copyable(),
                    ])
                    ->collapsible(),

                Section::make('System Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID')
                            ->copyable(),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Created'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->label('Last Updated'),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
