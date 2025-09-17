<?php

namespace App\Filament\Base\Resources\Airports;

use App\Filament\Base\Resources\Airports\Pages\CreateAirport;
use App\Filament\Base\Resources\Airports\Pages\EditAirport;
use App\Filament\Base\Resources\Airports\Pages\ListAirports;
use App\Filament\Base\Resources\Airports\Pages\ViewAirport;
use App\Filament\Base\Resources\Airports\Schemas\AirportForm;
use App\Filament\Base\Resources\Airports\Schemas\AirportInfolist;
use App\Filament\Base\Resources\Airports\Tables\AirportsTable;
use App\Models\Base\Airport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AirportResource extends Resource
{
    protected static ?string $model = Airport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Airports';

    protected static ?string $modelLabel = 'Airport';

    protected static ?string $pluralModelLabel = 'Airports';

    protected static string | UnitEnum | null $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 3;

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
        return $record->full_name;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'IATA Code' => $record->iata_code,
            'City' => $record->city->name ?? 'No city',
            'Country' => $record->city->province->country->name ?? 'No country',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'iata_code'];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function form(Schema $schema): Schema
    {
        return AirportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AirportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AirportsTable::configure($table);
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
            'index' => ListAirports::route('/'),
            'create' => CreateAirport::route('/create'),
            'view' => ViewAirport::route('/{record}'),
            'edit' => EditAirport::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['city.province.country']);
    }
}
