<?php

namespace App\Filament\Tenant\Resources\VehicleTypes;

use App\Filament\Tenant\Resources\VehicleTypes\Pages\CreateVehicleType;
use App\Filament\Tenant\Resources\VehicleTypes\Pages\EditVehicleType;
use App\Filament\Tenant\Resources\VehicleTypes\Pages\ListVehicleTypes;
use App\Filament\Tenant\Resources\VehicleTypes\Pages\ViewVehicleType;
use App\Filament\Tenant\Resources\VehicleTypes\Schemas\VehicleTypeForm;
use App\Filament\Tenant\Resources\VehicleTypes\Schemas\VehicleTypeInfolist;
use App\Filament\Tenant\Resources\VehicleTypes\Tables\VehicleTypesTable;
use App\Models\Tenants\VehicleType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Vehicle Types';

    protected static ?string $modelLabel = 'Vehicle Type';

    protected static ?string $pluralModelLabel = 'Vehicle Types';

    protected static string | UnitEnum | null $navigationGroup = 'Data Types';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->name . ' (' . ($record->vehicleCategory->name ?? 'No Category') . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Category' => $record->vehicleCategory->name ?? 'No category',
            'Capacity' => $record->capacity_from === $record->capacity_to 
                ? $record->capacity_from . ' passengers'
                : $record->capacity_from . '-' . $record->capacity_to . ' passengers',
            'VIP' => $record->is_vip ? 'VIP Service' : 'Standard Service',
            'Per Day Price' => $record->per_day_price ? '$' . number_format($record->per_day_price, 2) : 'Not specified',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['vehicleCategory']);
    }

    public static function form(Schema $schema): Schema
    {
        return VehicleTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VehicleTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleTypesTable::configure($table);
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
            'index' => ListVehicleTypes::route('/'),
            'create' => CreateVehicleType::route('/create'),
            'view' => ViewVehicleType::route('/{record}'),
            'edit' => EditVehicleType::route('/{record}/edit'),
        ];
    }
}
