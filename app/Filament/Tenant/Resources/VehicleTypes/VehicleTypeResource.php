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
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use UnitEnum;

class VehicleTypeResource extends Resource
{
    use Translatable;
    protected static ?string $model = VehicleType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static string | UnitEnum | null $navigationGroup = null;

    public static function getNavigationLabel(): string
    {
        return __('tenant-vehicle-types.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('tenant-vehicle-types.resource_name');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tenant-vehicle-types.resource_name_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('tenant-vehicle-types.navigation_group');
    }

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
        return $record->name . ' (' . ($record->vehicleCategory->name ?? __('tenant-vehicle-types.placeholders.no_category')) . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            __('tenant-vehicle-types.global_search.category') => $record->vehicleCategory->name ?? __('tenant-vehicle-types.placeholders.no_category'),
            __('tenant-vehicle-types.global_search.capacity') => $record->capacity_from === $record->capacity_to 
                ? $record->capacity_from . ' ' . __('tenant-vehicle-types.suffixes.passengers')
                : $record->capacity_from . '-' . $record->capacity_to . ' ' . __('tenant-vehicle-types.suffixes.passengers'),
            __('tenant-vehicle-types.global_search.vip') => $record->is_vip ? __('tenant-vehicle-types.global_search.vip_service') : __('tenant-vehicle-types.global_search.standard_service'),
            __('tenant-vehicle-types.global_search.per_day_price') => $record->per_day_price ? '$' . number_format($record->per_day_price, 2) : __('tenant-vehicle-types.placeholders.not_specified'),
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
