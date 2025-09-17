<?php

namespace App\Filament\Base\Resources\BorderPoints;

use App\Filament\Base\Resources\BorderPoints\Pages\CreateBorderPoint;
use App\Filament\Base\Resources\BorderPoints\Pages\EditBorderPoint;
use App\Filament\Base\Resources\BorderPoints\Pages\ListBorderPoints;
use App\Filament\Base\Resources\BorderPoints\Pages\ViewBorderPoint;
use App\Filament\Base\Resources\BorderPoints\Schemas\BorderPointForm;
use App\Filament\Base\Resources\BorderPoints\Schemas\BorderPointInfolist;
use App\Filament\Base\Resources\BorderPoints\Tables\BorderPointsTable;
use App\Models\Base\BorderPoint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BorderPointResource extends Resource
{
    protected static ?string $model = BorderPoint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Border Points';

    protected static ?string $modelLabel = 'Border Point';

    protected static ?string $pluralModelLabel = 'Border Points';

    protected static string | UnitEnum | null $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->full_name;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'IATA Code' => $record->iata_code ?? 'No IATA code',
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
        return BorderPointForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BorderPointInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BorderPointsTable::configure($table);
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
            'index' => ListBorderPoints::route('/'),
            'create' => CreateBorderPoint::route('/create'),
            'view' => ViewBorderPoint::route('/{record}'),
            'edit' => EditBorderPoint::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['city.province.country']);
    }
}
