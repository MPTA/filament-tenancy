<?php

namespace App\Filament\Tenant\Resources\ExchangeRates;

use App\Filament\Tenant\Resources\ExchangeRates\Pages\CreateExchangeRate;
use App\Filament\Tenant\Resources\ExchangeRates\Pages\EditExchangeRate;
use App\Filament\Tenant\Resources\ExchangeRates\Pages\ListExchangeRates;
use App\Filament\Tenant\Resources\ExchangeRates\Schemas\ExchangeRateForm;
use App\Filament\Tenant\Resources\ExchangeRates\Tables\ExchangeRatesTable;
use App\Models\Tenants\ExchangeRate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $recordTitleAttribute = 'rate';

    protected static ?int $navigationSort = 1;
    
    public static function getNavigationLabel(): string
    {
        return __('tenant-exchange-rates.navigation_label');
    }
    
    public static function getLabel(): ?string
    {
        return __('tenant-exchange-rates.resource_name');
    }
    
    public static function getPluralLabel(): ?string
    {
        return __('tenant-exchange-rates.resource_name_plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('tenant-exchange-rates.navigation_group');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->fromCurrency->name . ' ' . __('tenant-exchange-rates.global_search.to') . ' ' . $record->toCurrency->name . ' (' . $record->rate . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            __('tenant-exchange-rates.global_search.rate_label') => $record->rate,
            __('tenant-exchange-rates.global_search.from') => $record->fromCurrency->name ?? __('tenant-exchange-rates.global_search.unknown'),
            __('tenant-exchange-rates.global_search.to_label') => $record->toCurrency->name ?? __('tenant-exchange-rates.global_search.unknown'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['rate'];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('edit', ['record' => $record]);
    }

    public static function form(Schema $schema): Schema
    {
        return ExchangeRateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExchangeRatesTable::configure($table);
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
            'index' => ListExchangeRates::route('/'),
            'create' => CreateExchangeRate::route('/create'),
            'edit' => EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
