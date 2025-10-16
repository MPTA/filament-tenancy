<?php

namespace App\Filament\Tenant\Resources\TenantAccommodations\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TenantPricesRelationManager extends RelationManager
{
    protected static string $relationship = 'tenantPrices';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('tenant-prices.title');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Room Category and Price (Tenant Default Currency)
                Select::make('room_category_id')
                    ->label(__('common-fields.room_category'))
                    ->relationship('roomCategory', 'name')
                    ->searchable()
                    ->preload()
                    ->columnStart(1)
                    ->required()
                    ->rules(['required'])
                    ->validationMessages([
                        'required' => __('tenant-prices.validations.room_category_required'),
                    ])
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->form->validate(['room_category_id']);
                    }),
                TextInput::make('price')
                    ->label(__('common-fields.price'))
                    ->required()
                    ->numeric()
                    ->prefix(function () {
                        // Get currency symbol from tenant settings
                        return tenant()->settings?->country?->currency?->symbol ?? '$';
                    })
                    ->rules(['required', 'numeric', 'min:0'])
                    ->validationMessages([
                        'required' => __('tenant-prices.validations.price_required'),
                        'numeric' => __('tenant-prices.validations.price_numeric'),
                        'min' => __('tenant-prices.validations.price_min'),
                    ]),
                
                // Third line: Valid From and Valid To (2 fields)
                DatePicker::make('valid_from')
                    ->label(__('common-fields.valid_from'))
                    ->default(Carbon::now())
                    ->required()
                    ->rules(['required', 'date'])
                    ->validationMessages([
                        'required' => __('tenant-prices.validations.valid_from_required'),
                        'date' => __('tenant-prices.validations.valid_from_date'),
                    ])
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->form->validate(['valid_from']);
                    }),
                DatePicker::make('valid_to')
                    ->label(__('common-fields.valid_to'))
                    ->after('valid_from')
                    ->placeholder(__('tenant-prices.placeholders.valid_to')),
                
                
                // Fourth line: Meal Inclusion section only
                Section::make(__('tenant-prices.sections.meal_inclusion.title'))
                    ->description(__('tenant-prices.sections.meal_inclusion.description'))
                    ->icon('heroicon-o-cake')
                    ->compact()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_include_breakfast')
                                    ->label(__('common-fields.breakfast'))
                                    ->default(true)
                                    ->inline(false),
                                Toggle::make('is_include_lunch')
                                    ->label(__('common-fields.lunch'))
                                    ->default(false)
                                    ->inline(false),
                                Toggle::make('is_include_dinner')
                                    ->label(__('common-fields.dinner'))
                                    ->default(false)
                                    ->inline(false),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistGrid::make(2)
                    ->schema([
                        TextEntry::make('roomCategory.name')
                            ->label(__('common-fields.room_category'))
                            ->badge(),
                        TextEntry::make('price')
                            ->label(__('common-fields.price'))
                            ->money()
                            ->badge()
                            ->color('success'),
                    ]),
                InfolistGrid::make(2)
                    ->schema([
                        TextEntry::make('currency.name')
                            ->label(__('common-fields.currency'))
                            ->badge(),
                        TextEntry::make('valid_from')
                            ->label(__('common-fields.valid_from'))
                            ->date()
                            ->badge()
                            ->color('info'),
                    ]),
                TextEntry::make('valid_to')
                    ->label(__('common-fields.valid_to'))
                    ->date()
                    ->placeholder(__('tenant-prices.messages.indefinite_validity'))
                    ->badge()
                    ->color('warning'),
                InfolistGrid::make(3)
                    ->schema([
                        TextEntry::make('is_include_breakfast')
                            ->label(__('common-fields.breakfast'))
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn($state) => $state ? __('tenant-prices.messages.included') : __('tenant-prices.messages.not_included')),
                        TextEntry::make('is_include_lunch')
                            ->label(__('common-fields.lunch'))
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn($state) => $state ? __('tenant-prices.messages.included') : __('tenant-prices.messages.not_included')),
                        TextEntry::make('is_include_dinner')
                            ->label(__('common-fields.dinner'))
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn($state) => $state ? __('tenant-prices.messages.included') : __('tenant-prices.messages.not_included')),
                    ]),
                InfolistGrid::make(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('common-fields.created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(__('common-fields.updated_at'))
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
                    ->label(__('common-fields.room_category'))
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('price')
                    ->label(__('common-fields.price'))
                    ->money(fn($record) => $record->currency?->code )
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('currency.symbol')
                    ->label(__('common-fields.currency'))
                    ->searchable()
                    ->badge(),
                TextColumn::make('valid_from')
                    ->label(__('common-fields.valid_from'))
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('valid_to')
                    ->label(__('common-fields.valid_to'))
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->placeholder(__('tenant-prices.messages.indefinite')),
                TextColumn::make('is_include_breakfast')
                    ->label(__('tenant-prices.columns.meals_included'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($record) {
                        $meals = [];
                        if ($record->is_include_breakfast) $meals[] = __('common-fields.breakfast');
                        if ($record->is_include_lunch) $meals[] = __('common-fields.lunch');
                        if ($record->is_include_dinner) $meals[] = __('common-fields.dinner');
                        return empty($meals) ? __('tenant-prices.messages.no_meals') : implode(', ', $meals);
                    }),
                TextColumn::make('status')
                    ->label(__('common-fields.status'))
                    ->badge()
                    ->color(fn($record) => $record->isValidForDate() ? 'success' : 'danger')
                    ->formatStateUsing(fn($record) => $record->isValidForDate() ? __('tenant-prices.messages.active') : __('tenant-prices.messages.expired')),
            ])
            ->filters([
                SelectFilter::make('room_category_id')
                    ->label(__('common-fields.room_category'))
                    ->relationship('roomCategory', 'name'),
                Filter::make('active')
                    ->label(__('tenant-prices.filters.active_prices'))
                    ->query(fn(Builder $query) => $query->where('valid_from', '<=', Carbon::now())
                                                      ->where(function ($q) {
                                                          $q->whereNull('valid_to')
                                                            ->orWhere('valid_to', '>=', Carbon::now());
                                                      })),
                Filter::make('expired')
                    ->label(__('tenant-prices.filters.expired_prices'))
                    ->query(fn(Builder $query) => $query->where('valid_to', '<', Carbon::now())),
                Filter::make('with_breakfast')
                    ->label(__('tenant-prices.filters.with_breakfast'))
                    ->query(fn(Builder $query) => $query->where('is_include_breakfast', true)),
                Filter::make('with_lunch')
                    ->label(__('tenant-prices.filters.with_lunch'))
                    ->query(fn(Builder $query) => $query->where('is_include_lunch', true)),
                Filter::make('with_dinner')
                    ->label(__('tenant-prices.filters.with_dinner'))
                    ->query(fn(Builder $query) => $query->where('is_include_dinner', true)),
            ])
            ->emptyStateHeading(__('tenant-prices.empty_state.heading'))
            ->emptyStateDescription(__('tenant-prices.empty_state.description'))
            ->defaultSort('valid_from', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label(__('tenant-prices.actions.add_price'))
                    ->beforeFormFilled(function () {
                        $this->form->fill(['accommodation_id' => $this->ownerRecord->id]);
                    })
                    ->before(function (array $data, $action) {
                        // Check for duplicate before saving (including meal inclusion)
                        $exists = \App\Models\Tenants\TenantAccommodationPrice::query()
                            ->where('tenant_id', tenant('id'))
                            ->where('accommodation_id', $this->ownerRecord->id)
                            ->where('room_category_id', $data['room_category_id'])
                            ->where('valid_from', $data['valid_from'])
                            ->where('is_include_breakfast', $data['is_include_breakfast'] ?? false)
                            ->where('is_include_lunch', $data['is_include_lunch'] ?? false)
                            ->where('is_include_dinner', $data['is_include_dinner'] ?? false)
                            ->exists();
                        
                        if ($exists) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('tenant-prices.notifications.duplicate_price_title'))
                                ->body(__('tenant-prices.notifications.duplicate_price_body'))
                                ->danger()
                                ->send();
                            
                            $action->halt();
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
