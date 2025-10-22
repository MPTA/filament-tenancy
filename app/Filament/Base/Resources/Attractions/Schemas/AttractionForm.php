<?php

namespace App\Filament\Base\Resources\Attractions\Schemas;

use App\Enums\AttractionTypeEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttractionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Select::make('type')
                            ->options(AttractionTypeEnum::getOptions())
                            ->required()
                            ->searchable(),
                        Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Location Information')
                    ->schema([
                        Select::make('country_id')
                            ->relationship('country', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                        Select::make('city_id')
                            ->relationship('city', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('district_id', null)),
                        Select::make('district_id')
                            ->relationship('district', 'name')
                            ->searchable()
                            ->preload(),
                        Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Coordinates & Rating')
                    ->schema([
                        TextInput::make('latitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('e.g., 39.9042'),
                        TextInput::make('longitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('e.g., 116.4074'),
                        TextInput::make('rating')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(5)
                            ->suffix('/ 5'),
                    ])
                    ->columns(3),

                Section::make('Pricing Information')
                    ->schema([
                        TextInput::make('local_price')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->label('Local Price'),
                        TextInput::make('foreigner_price')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->label('Foreigner Price'),
                    ])
                    ->columns(2),

                Section::make('Visit Information')
                    ->schema([
                        Textarea::make('opening_hours')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('suggested_duration')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('duration_hours.min')
                            ->numeric()
                            ->label('Min Hours')
                            ->placeholder('e.g., 2'),
                        TextInput::make('duration_hours.max')
                            ->numeric()
                            ->label('Max Hours')
                            ->placeholder('e.g., 4'),
                        Textarea::make('suggested_season')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Seasonal Availability')
                    ->schema([
                        CheckboxList::make('seasons')
                            ->options([
                                'spring' => 'Spring',
                                'summer' => 'Summer',
                                'autumn' => 'Autumn',
                                'winter' => 'Winter',
                            ])
                            ->columns(4)
                            ->columnSpanFull()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $seasons = [];
                                    if ($record->season_spring) $seasons[] = 'spring';
                                    if ($record->season_summer) $seasons[] = 'summer';
                                    if ($record->season_autumn) $seasons[] = 'autumn';
                                    if ($record->season_winter) $seasons[] = 'winter';
                                    $component->state($seasons);
                                }
                            })
                            ->dehydrated(false),
                        Toggle::make('season_spring')
                            ->label('Spring')
                            ->visible(fn ($get) => in_array('spring', $get('seasons') ?? [])),
                        Toggle::make('season_summer')
                            ->label('Summer')
                            ->visible(fn ($get) => in_array('summer', $get('seasons') ?? [])),
                        Toggle::make('season_autumn')
                            ->label('Autumn')
                            ->visible(fn ($get) => in_array('autumn', $get('seasons') ?? [])),
                        Toggle::make('season_winter')
                            ->label('Winter')
                            ->visible(fn ($get) => in_array('winter', $get('seasons') ?? [])),
                    ])
                    ->columns(4)
                    ->collapsible(),

                Section::make('Ticket & Tips Information')
                    ->schema([
                        Textarea::make('ticket_info')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('tips')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('External Links')
                    ->schema([
                        TextInput::make('link')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://example.com')
                            ->columnSpanFull(),
                        TextInput::make('image_url')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://example.com/image.jpg')
                            ->columnSpanFull(),
                        TextInput::make('external_id')
                            ->maxLength(255)
                            ->label('External ID'),
                    ])
                    ->collapsible(),
            ]);
    }
}
