<?php

namespace App\Filament\App\Resources\ExchangeRates;

use App\Filament\App\Resources\ExchangeRates\Pages\CreateExchangeRate;
use App\Filament\App\Resources\ExchangeRates\Pages\EditExchangeRate;
use App\Filament\App\Resources\ExchangeRates\Pages\ListExchangeRates;
use App\Filament\App\Resources\ExchangeRates\Schemas\ExchangeRateForm;
use App\Filament\App\Resources\ExchangeRates\Tables\ExchangeRatesTable;
use App\Models\Tenants\ExchangeRate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'rate';

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
