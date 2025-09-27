<?php

namespace App\Filament\Base\Resources\Attractions\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section as FormSection;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\RelationManagers\Concerns\Translatable;

class SubAttractionsRelationManager extends RelationManager
{
    use Translatable;
    protected static string $relationship = 'subAttractions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormSection::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                FormSection::make('Location Coordinates')
                    ->schema([
                        TextInput::make('latitude')
                            ->numeric()
                            ->step(0.000001)
                            ->minValue(-90)
                            ->maxValue(90)
                            ->suffix('°'),
                        TextInput::make('longitude')
                            ->numeric()
                            ->step(0.000001)
                            ->minValue(-180)
                            ->maxValue(180)
                            ->suffix('°'),
                    ])
                    ->columns(2),

                FormSection::make('Pricing Information')
                    ->schema([
                        TextInput::make('price')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('$')
                            ->suffix('USD')
                            ->helperText('General price for all visitors'),
                        TextInput::make('local_price')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('$')
                            ->suffix('USD')
                            ->helperText('Price for local visitors'),
                        TextInput::make('foreigner_price')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('$')
                            ->suffix('USD')
                            ->helperText('Price for foreign visitors'),
                    ])
                    ->columns(3),
            ]);
    }

    public function infolist(Schema $schema): Schema
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
                    ])
                    ->columns(2),

                Section::make('Location Coordinates')
                    ->schema([
                        TextEntry::make('latitude')
                            ->numeric()
                            ->placeholder('Not specified')
                            ->suffix('°'),
                        TextEntry::make('longitude')
                            ->numeric()
                            ->placeholder('Not specified')
                            ->suffix('°'),
                    ])
                    ->columns(2),

                Section::make('Pricing Information')
                    ->schema([
                        TextEntry::make('price')
                            ->money('USD')
                            ->placeholder('Free')
                            ->label('General Price'),
                        TextEntry::make('local_price')
                            ->money('USD')
                            ->placeholder('Free')
                            ->label('Local Price'),
                        TextEntry::make('foreigner_price')
                            ->money('USD')
                            ->placeholder('Free')
                            ->label('Foreigner Price'),
                    ])
                    ->columns(3),

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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),
                TextColumn::make('description')
                    ->limit(50)
                    ->placeholder('No description')
                    ->toggleable(),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('Free'),
                TextColumn::make('local_price')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('Free')
                    ->toggleable(),
                TextColumn::make('foreigner_price')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('Free')
                    ->toggleable(),
                TextColumn::make('coordinates')
                    ->getStateUsing(function ($record) {
                        if ($record->latitude && $record->longitude) {
                            return number_format($record->latitude, 6) . ', ' . number_format($record->longitude, 6);
                        }
                        return 'Not specified';
                    })
                    ->label('Coordinates')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
