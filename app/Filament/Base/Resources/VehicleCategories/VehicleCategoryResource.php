<?php

namespace App\Filament\Base\Resources\VehicleCategories;

use App\Filament\Base\Resources\VehicleCategories\Pages\CreateVehicleCategory;
use App\Filament\Base\Resources\VehicleCategories\Pages\EditVehicleCategory;
use App\Filament\Base\Resources\VehicleCategories\Pages\ListVehicleCategories;
use App\Filament\Base\Resources\VehicleCategories\Pages\ViewVehicleCategory;
use App\Filament\Base\Resources\VehicleCategories\Schemas\VehicleCategoryForm;
use App\Filament\Base\Resources\VehicleCategories\Schemas\VehicleCategoryInfolist;
use App\Filament\Base\Resources\VehicleCategories\Tables\VehicleCategoriesTable;
use App\Models\Base\VehicleCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VehicleCategoryResource extends Resource
{
    protected static ?string $model = VehicleCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VehicleCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VehicleCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleCategoriesTable::configure($table);
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
            'index' => ListVehicleCategories::route('/'),
            'create' => CreateVehicleCategory::route('/create'),
            'view' => ViewVehicleCategory::route('/{record}'),
            'edit' => EditVehicleCategory::route('/{record}/edit'),
        ];
    }
}
