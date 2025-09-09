<?php

namespace App\Filament\Base\Resources\Countries\RelationManagers;

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
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use LaraZeus\SpatieTranslatable\Resources\RelationManagers\Concerns\Translatable;

class ProvincesRelationManager extends RelationManager
{
    use Translatable;
    protected static string $relationship = 'provinces';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Province Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Province Name')
                            ->required()
                            ->maxLength(255)
                            ->hint('Enter province name in different languages'),
                        TextInput::make('code')
                            ->label('Province Code')
                            ->maxLength(10)
                            ->hint('Optional province code or abbreviation')
                            ->placeholder('e.g., CA, NY, TX'),
                    ])
                    ->columns(1),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Province Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Province Name'),
                        TextEntry::make('code')
                            ->label('Province Code')
                            ->badge()
                            ->color('info')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Statistics')
                    ->schema([
                        TextEntry::make('cities_count')
                            ->label('Cities')
                            ->badge()
                            ->color('success')
                            ->state(fn ($record) => $record->cities()->count()),
                        TextEntry::make('districts_count')
                            ->label('Districts')
                            ->badge()
                            ->color('info')
                            ->state(fn ($record) => $record->districts()->count()),
                        TextEntry::make('attractions_count')
                            ->label('Attractions')
                            ->badge()
                            ->color('warning')
                            ->state(fn ($record) => $record->attractions()->count()),
                        TextEntry::make('accommodations_count')
                            ->label('Accommodations')
                            ->badge()
                            ->color('primary')
                            ->state(fn ($record) => $record->accommodations()->count()),
                    ])
                    ->columns(2),

                Section::make('System Information')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Province Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable(),
                TextColumn::make('cities_count')
                    ->label('Cities')
                    ->counts('cities')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('districts_count')
                    ->label('Districts')
                    ->counts('districts')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('attractions_count')
                    ->label('Attractions')
                    ->counts('attractions')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('accommodations_count')
                    ->label('Accommodations')
                    ->counts('accommodations')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_cities')
                    ->label('Has Cities')
                    ->query(fn (Builder $query): Builder => $query->has('cities')),
                Filter::make('has_districts')
                    ->label('Has Districts')
                    ->query(fn (Builder $query): Builder => $query->has('districts')),
                Filter::make('has_attractions')
                    ->label('Has Attractions')
                    ->query(fn (Builder $query): Builder => $query->has('attractions')),
                Filter::make('has_accommodations')
                    ->label('Has Accommodations')
                    ->query(fn (Builder $query): Builder => $query->has('accommodations')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Province'),
                AssociateAction::make()
                    ->label('Associate Province'),
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
            ])
            ->defaultSort('name');
    }
}
