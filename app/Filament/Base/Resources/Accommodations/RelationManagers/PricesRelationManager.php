<?php

namespace App\Filament\Base\Resources\Accommodations\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid as InfolistGrid;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('room_category_id')
                            ->label('Room Category')
                            ->relationship('roomCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('currency_id')
                            ->label('Currency')
                            ->relationship('currency', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                Grid::make(2)
                    ->schema([
                        TextInput::make('price')
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->prefix(fn($record) => $record?->currency?->symbol ?? '$')
                            ->rules(['required', 'numeric', 'min:0'])
                            ->validationMessages([
                                'required' => 'Price is required',
                                'numeric' => 'Price must be a number',
                                'min' => 'Price cannot be negative',
                            ]),
                        DatePicker::make('valid_from')
                            ->label('Valid From')
                            ->default(Carbon::now())
                            ->required(),
                    ]),
                DatePicker::make('valid_to')
                    ->label('Valid To')
                    ->after('valid_from')
                    ->placeholder('Leave empty for indefinite validity'),
                
                Section::make('Meal Inclusion')
                    ->description('Select which meals are included in this price')
                    ->icon('heroicon-o-utensils')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_include_breakfast')
                                    ->label('Include Breakfast')
                                    ->default(false)
                                    ->helperText('Breakfast is included in the price'),
                                Toggle::make('is_include_lunch')
                                    ->label('Include Lunch')
                                    ->default(false)
                                    ->helperText('Lunch is included in the price'),
                                Toggle::make('is_include_dinner')
                                    ->label('Include Dinner')
                                    ->default(false)
                                    ->helperText('Dinner is included in the price'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistGrid::make(2)
                    ->schema([
                        TextEntry::make('roomCategory.name')
                            ->label('Room Category')
                            ->badge(),
                        TextEntry::make('price')
                            ->label('Price')
                            ->money()
                            ->badge()
                            ->color('success'),
                    ]),
                InfolistGrid::make(2)
                    ->schema([
                        TextEntry::make('currency.name')
                            ->label('Currency')
                            ->badge(),
                        TextEntry::make('valid_from')
                            ->label('Valid From')
                            ->date()
                            ->badge()
                            ->color('info'),
                    ]),
                TextEntry::make('valid_to')
                    ->label('Valid To')
                    ->date()
                    ->placeholder('Indefinite validity')
                    ->badge()
                    ->color('warning'),
                InfolistGrid::make(3)
                    ->schema([
                        TextEntry::make('is_include_breakfast')
                            ->label('Breakfast')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn($state) => $state ? 'Included' : 'Not Included'),
                        TextEntry::make('is_include_lunch')
                            ->label('Lunch')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn($state) => $state ? 'Included' : 'Not Included'),
                        TextEntry::make('is_include_dinner')
                            ->label('Dinner')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn($state) => $state ? 'Included' : 'Not Included'),
                    ]),
                InfolistGrid::make(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('roomCategory.name')
            ->columns([
                TextColumn::make('roomCategory.name')
                    ->label('Room Category')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('price')
                    ->label('Price')
                    ->money()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('currency.name')
                    ->label('Currency')
                    ->searchable()
                    ->badge(),
                TextColumn::make('valid_from')
                    ->label('Valid From')
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('valid_to')
                    ->label('Valid To')
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->placeholder('Indefinite'),
                TextColumn::make('meals')
                    ->label('Meals Included')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($record) {
                        $meals = [];
                        if ($record->is_include_breakfast) $meals[] = 'Breakfast';
                        if ($record->is_include_lunch) $meals[] = 'Lunch';
                        if ($record->is_include_dinner) $meals[] = 'Dinner';
                        return empty($meals) ? 'No meals' : implode(', ', $meals);
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($record) => $record->isValidForDate() ? 'success' : 'danger')
                    ->formatStateUsing(fn($record) => $record->isValidForDate() ? 'Active' : 'Expired'),
            ])
            ->filters([
                SelectFilter::make('room_category_id')
                    ->label('Room Category')
                    ->relationship('roomCategory', 'name'),
                SelectFilter::make('currency_id')
                    ->label('Currency')
                    ->relationship('currency', 'name'),
                Filter::make('active')
                    ->label('Active Prices')
                    ->query(fn(Builder $query) => $query->where('valid_from', '<=', Carbon::now())
                                                      ->where(function ($q) {
                                                          $q->whereNull('valid_to')
                                                            ->orWhere('valid_to', '>=', Carbon::now());
                                                      })),
                Filter::make('expired')
                    ->label('Expired Prices')
                    ->query(fn(Builder $query) => $query->where('valid_to', '<', Carbon::now())),
                Filter::make('with_breakfast')
                    ->label('With Breakfast')
                    ->query(fn(Builder $query) => $query->where('is_include_breakfast', true)),
                Filter::make('with_lunch')
                    ->label('With Lunch')
                    ->query(fn(Builder $query) => $query->where('is_include_lunch', true)),
                Filter::make('with_dinner')
                    ->label('With Dinner')
                    ->query(fn(Builder $query) => $query->where('is_include_dinner', true)),
            ])
            ->defaultSort('valid_from', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Add Price'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
