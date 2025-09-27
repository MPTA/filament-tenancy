<?php

namespace App\Filament\Base\Resources\Attractions\Schemas;

use App\Enums\AttractionTypeEnum;
use Filament\Infolists\Components\IconEntry;
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
                            ->color(fn (string $state): string => match ($state) {
                                'natural' => 'success',
                                'man_made' => 'info',
                                'cultural' => 'warning',
                                'sport' => 'danger',
                                'events' => 'primary',
                                'leisure' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => AttractionTypeEnum::from($state)->label()),
                        IconEntry::make('is_active')
                            ->boolean()
                            ->label('Status'),
                    ])
                    ->columns(2),

                Section::make('Location Information')
                    ->schema([
                        TextEntry::make('country.name')
                            ->label('Country'),
                        TextEntry::make('province.name')
                            ->label('Province'),
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
                            ->money('USD')
                            ->placeholder('Free'),
                        TextEntry::make('foreigner_price')
                            ->money('USD')
                            ->placeholder('Free'),
                    ])
                    ->columns(2),

                Section::make('External Integration')
                    ->schema([
                        TextEntry::make('external_id')
                            ->label('External ID')
                            ->placeholder('Not specified'),
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
