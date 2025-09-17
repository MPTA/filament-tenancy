<?php

namespace App\Filament\Base\Resources\Currencies;

use App\Filament\Base\Resources\Currencies\Pages\CreateCurrency;
use App\Filament\Base\Resources\Currencies\Pages\EditCurrency;
use App\Filament\Base\Resources\Currencies\Pages\ListCurrencies;
use App\Filament\Base\Resources\Currencies\Pages\ViewCurrency;
use App\Filament\Base\Resources\Currencies\Schemas\CurrencyForm;
use App\Filament\Base\Resources\Currencies\Schemas\CurrencyInfolist;
use App\Filament\Base\Resources\Currencies\Tables\CurrenciesTable;
use App\Models\Base\Currency;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Currencies';

    protected static ?string $modelLabel = 'Currency';

    protected static ?string $pluralModelLabel = 'Currencies';

    protected static string | UnitEnum | null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->name . ' (' . $record->code . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Code' => $record->code,
            'Symbol' => $record->symbol ?? 'No symbol',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'name', 'symbol'];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function form(Schema $schema): Schema
    {
        return CurrencyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CurrencyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CurrenciesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurrencies::route('/'),
            'create' => CreateCurrency::route('/create'),
            'view' => ViewCurrency::route('/{record}'),
            'edit' => EditCurrency::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery();
    }
}
